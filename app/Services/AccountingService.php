<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Shipment;
use App\Models\AgentSettlement;
use App\Models\Payroll;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create Journal Entry for Payroll
     */
    public function createPayrollEntry(Payroll $payroll)
    {
        $expenseAccount = ChartOfAccount::where('code', '5101')->first(); // Salaries Expense
        $cashAccount = ChartOfAccount::where('code', '1101')->first(); // Cash/Vault (Simplified)

        if (!$expenseAccount || !$cashAccount) {
            \Log::warning("Accounting Service: Missing accounts for Payroll #{$payroll->id}");
            return;
        }

        DB::transaction(function () use ($payroll, $expenseAccount, $cashAccount) {
            $entry = JournalEntry::create([
                'entry_date' => now(),
                'description' => "Payroll Posting: {$payroll->reference_number} - Month: {$payroll->month}/{$payroll->year}",
                'type' => 'automatic',
                'reference_type' => Payroll::class,
                'reference_id' => $payroll->id,
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
            ]);

            // Debit Salaries Expense
            $entry->lines()->create([
                'account_id' => $expenseAccount->id,
                'debit' => $payroll->total_amount,
                'credit' => 0,
                'description' => "Salaries for period {$payroll->month}/{$payroll->year}",
            ]);

            // Credit Cash (Payment)
            $entry->lines()->create([
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $payroll->total_amount,
                'description' => "Payment of salaries {$payroll->reference_number}",
            ]);

            $payroll->update([
                'journal_entry_id' => $entry->id,
                'status' => 'paid'
            ]);
        });
    }
    /**
     * Create Journal Entry for Agent Settlement (Remittance)
     */
    public function createSettlementEntry(AgentSettlement $settlement)
    {
        $agent = $settlement->agent;
        $agentAccount = $this->getAgentAccount($agent); // Cr. Agent Receivable
        $receivingAccount = $settlement->receivingAccount; // Dr. Cash/Bank

        if (!$agentAccount || !$receivingAccount) {
            \Log::warning("Accounting Service: Missing accounts for Settlement #{$settlement->id}");
            return;
        }

        DB::transaction(function () use ($settlement, $agentAccount, $receivingAccount) {
            $entry = JournalEntry::create([
                'entry_date' => $settlement->settlement_date,
                'description' => "Agent Settlement #{$settlement->reference_number} - From: {$settlement->agent->name}",
                'type' => 'automatic',
                'reference_type' => AgentSettlement::class,
                'reference_id' => $settlement->id,
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
            ]);

            // Debit Cash/Bank
            $entry->lines()->create([
                'account_id' => $receivingAccount->id,
                'debit' => $settlement->amount,
                'credit' => 0,
                'description' => "Remittance from {$settlement->agent->name}",
            ]);

            // Credit Agent
            $entry->lines()->create([
                'account_id' => $agentAccount->id,
                'debit' => 0,
                'credit' => $settlement->amount,
                'description' => $settlement->reference_number,
            ]);

            $settlement->update([
                'journal_entry_id' => $entry->id,
                'status' => 'confirmed'
            ]);
        });
    }
    /**
     * Create Journal Entry for Shipment Delivery
     */
    public function createShipmentEntry(Shipment $shipment)
    {
        // Only trigger for 'delivered' status or partial delivery
        if (!$shipment->status || $shipment->status->code !== 'delivered') {
            return;
        }

        // We need an agent and their financial account
        if (!$shipment->delivery_agent_id) {
            \Log::warning("Accounting Service: Shipment #{$shipment->id} delivered without agent.");
            return;
        }

        $agent = $shipment->deliveryAgent;
        $agentAccount = $this->getAgentAccount($agent);
        $revenueAccount = ChartOfAccount::where('code', '4100')->first(); // Sales/Shipping Revenue

        if (!$agentAccount || !$revenueAccount) {
            \Log::warning("Accounting Service: Missing accounts for Shipment #{$shipment->id}");
            return;
        }

        DB::transaction(function () use ($shipment, $agentAccount, $revenueAccount) {
            $entry = JournalEntry::create([
                'entry_date' => $shipment->delivery_date ?? now(),
                'description' => "Shipment #{$shipment->tracking_number} Delivered - Customer: {$shipment->customer_name}",
                'type' => 'automatic',
                'reference_type' => Shipment::class,
                'reference_id' => $shipment->id,
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
            ]);

            // Debit Agent (Collection Responsibility)
            $entry->lines()->create([
                'account_id' => $agentAccount->id,
                'debit' => $shipment->total_amount,
                'credit' => 0,
                'description' => "Collected amount for {$shipment->tracking_number}",
            ]);

            // Credit Revenue
            $entry->lines()->create([
                'account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => $shipment->total_amount,
                'description' => "Revenue from Shipment {$shipment->tracking_number}",
            ]);
        });
    }

    /**
     * Ensure agent has a financial account
     */
    public function getAgentAccount($agent)
    {
        if ($agent->account_id) {
            return $agent->account;
        }

        $parent = ChartOfAccount::where('code', '1106')->first();
        if (!$parent) {
            return null;
        }

        // Create a unique account for the agent
        $code = '1106.' . str_pad($agent->id, 4, '0', STR_PAD_LEFT);
        
        $account = ChartOfAccount::firstOrCreate(
            ['code' => $code],
            [
                'name_ar' => 'عهد مندوب: ' . $agent->name,
                'name_en' => 'Agent Debt: ' . $agent->name,
                'parent_id' => $parent->id,
                'type' => 'asset',
                'nature' => 'debit',
                'level' => 4,
            ]
        );

        $agent->update(['account_id' => $account->id]);
        
        return $account;
    }
    public function createInvoiceEntry(Invoice $invoice)
    {
        // 1. Get Accounts
        $arAccount = ChartOfAccount::where('code', '1103')->first(); // AR
        $salesAccount = ChartOfAccount::where('code', '4100')->first(); // Revenue
        $taxAccount = ChartOfAccount::where('code', '2102')->first(); // VAT

        if (!$arAccount || !$salesAccount || !$taxAccount) {
            // Log warning or throw error if accounts not found
            // For now, logging and skipping to avoid breaking invoice flow
            \Log::warning("Accounting Service: Missing chart of accounts for Invoice #{$invoice->id}");
            return;
        }

        // 2. Prepare Entry Data
        // Dr. AR (Total)
        //   Cr. Sales (Subtotal)
        //   Cr. Tax (Tax Amount)
        
        DB::transaction(function () use ($invoice, $arAccount, $salesAccount, $taxAccount) {
            $entry = JournalEntry::create([
                'entry_date' => $invoice->issue_date,
                'description' => "Invoice #{$invoice->invoice_number} - {$invoice->customer_name}",
                'type' => 'automatic',
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
                'status' => 'posted', // Auto-post
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
            ]);

            // Debit AR
            $entry->lines()->create([
                'account_id' => $arAccount->id,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'description' => "Invoice #{$invoice->invoice_number}",
            ]);

            // Credit Sales
            if ($invoice->subtotal > 0) {
                $entry->lines()->create([
                    'account_id' => $salesAccount->id,
                    'debit' => 0,
                    'credit' => $invoice->subtotal,
                    'description' => "Sales Revenue",
                ]);
            }

            // Credit Tax
            if ($invoice->tax_amount > 0) {
                $entry->lines()->create([
                    'account_id' => $taxAccount->id,
                    'debit' => 0,
                    'credit' => $invoice->tax_amount,
                    'description' => "VAT 14%",
                ]);
            }
            
            // Check discount (if any)
            // If discount exists, it usually reduces the AR or is tracked separately.
            // Simplified here: Net Sales = Total - Tax. 
            // If subtotal is Gross, and Discount is applied, we might need a Discount Expense account.
            // For now assuming Subtotal is after line discounts, or simplified structure.
            
            // Let's ensure it handles 'discount_amount' from Invoice model if needed.
            // Invoice Model: total = subtotal + tax - discount.
            // So:
            // Dr. AR (Total)
            // Dr. Discount Expense (Discount)
            //   Cr. Sales (Subtotal)
            //   Cr. Tax (Tax)
            
            if ($invoice->discount_amount > 0) {
                 // We need a discount account, let's say 'Sales Discount' (Contra Revenue)
                 // For now, let's put it as a placeholder or adjust
                 // If we strictly follow the equation: Total + Discount = Subtotal + Tax
                 // We need to Debit the discount
                 
                // Warning: we need a Discount Account in Seeder? 
                // Let's assume we skip precise discount mapping for this moment or create it dynamically.
                // Or just adjust the Credits to balance.
                
                // Better approach: Create 'Sales Discount' account if not exists
                 $entry->lines()->create([
                    // using AR account temporarily if no discount account, or create one.
                    // Let's create a 'Sales Discounts' account 4300 if possible.
                    'account_id' => $this->getDiscountAccount()->id,
                    'debit' => $invoice->discount_amount,
                    'credit' => 0,
                    'description' => "Discount",
                ]);
            }
        });
    }
    
    public function createExpenseEntry(Expense $expense)
    {
        // Dr. Expense Account
        //   Cr. Accounts Payable OR Cash/Bank (if paid)
        
        // If not paid yet, use Accounts Payable (2101 - Suppliers/Payable)
        // Or a generic "Other Payables" account.
        // Assuming 2101 for now or filtered by type.
        
        $creditAccount = $expense->paid_via_account_id 
            ? ChartOfAccount::find($expense->paid_via_account_id)
            : ChartOfAccount::where('code', '2101')->first(); // Accounts Payable

        if (!$expense->expenseAccount || !$creditAccount) {
            \Log::warning("Accounting Service: Missing accounts for Expense #{$expense->id}");
            return;
        }

        DB::transaction(function () use ($expense, $creditAccount) {
            $entry = JournalEntry::create([
                'entry_date' => $expense->expense_date,
                'description' => "Expense #{$expense->reference_number} - {$expense->description}",
                'type' => 'automatic',
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
            ]);

            // Debit Expense
            $entry->lines()->create([
                'account_id' => $expense->expense_account_id,
                'debit' => $expense->amount,
                'credit' => 0,
                'description' => $expense->description,
            ]);

            // Credit Payable/Cash
            $entry->lines()->create([
                'account_id' => $creditAccount->id,
                'debit' => 0,
                'credit' => $expense->amount,
                'description' => $expense->reference_number,
            ]);
        });
    }

    private function getDiscountAccount()
    {
        return ChartOfAccount::firstOrCreate(
            ['code' => '4300'],
            [
                'name_ar' => 'خصومات مسموح بها',
                'name_en' => 'Sales Discounts',
                'type' => 'revenue',
                'nature' => 'debit',
                'parent_id' => ChartOfAccount::where('code', '4000')->first()->id ?? null,
                'level' => 2
            ]
        );
    }
}
