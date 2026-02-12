<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialStatementsService
{
    /**
     * Generate Income Statement
     */
    public function getIncomeStatement(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfYear();
        $endDate = $endDate ?? Carbon::now()->endOfDay();

        // 1. Revenue
        $revenues = $this->getAccountBalances('revenue', $startDate, $endDate);
        $totalRevenue = $revenues->sum('balance');

        // 2. Cost of Goods Sold (Expenses with code starting 52)
        // Adjust logic if COGS is strictly defined, for now using 'expense' type and COGS tree
        // Assuming COGS is under '5200' based on seeder
        $cogsAccounts = $this->getAccountBalances('expense', $startDate, $endDate, '52');
        $totalCOGS = $cogsAccounts->sum('balance');

        $grossProfit = $totalRevenue - $totalCOGS;

        // 3. Operating Expenses (All other expenses)
        // Exclude COGS from general expenses list
        $expenses = $this->getAccountBalances('expense', $startDate, $endDate);
        // Filter out COGS if fetched all
        $operatingExpenses = $expenses->filter(fn($acc) => !str_starts_with($acc['code'], '52'));
        $totalExpenses = $operatingExpenses->sum('balance');

        $netIncome = $grossProfit - $totalExpenses;

        return [
            'revenues' => $revenues,
            'total_revenue' => $totalRevenue,
            'cogs' => $cogsAccounts,
            'total_cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            'expenses' => $operatingExpenses,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'date_range' => [
                 'start' => $startDate->format('Y-m-d'),
                 'end' => $endDate->format('Y-m-d'),
            ]
        ];
    }

    /**
     * Generate Balance Sheet
     */
    public function getBalanceSheet(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::now()->endOfDay();

        // Assets
        $assets = $this->getAccountBalances('asset', null, $date);
        $totalAssets = $assets->sum('balance');

        // Liabilities
        $liabilities = $this->getAccountBalances('liability', null, $date);
        $totalLiabilities = $liabilities->sum('balance');

        // Equity
        $equity = $this->getAccountBalances('equity', null, $date);
        $totalEquity = $equity->sum('balance');

        // Net Income (Current Year Earnings) needs to be added to Equity to balance
        // Calculate Net Income from beginning of operations (or fiscal year) up to date?
        // Usually Retained Earnings + Current Year Net Income
        // For simplicity, we adding a calculated "Net Income" line to Equity
        
        $incomeStatement = $this->getIncomeStatement(null, $date);
        $currentNetIncome = $incomeStatement['net_income'];
        
        $totalEquityAndLiabilities = $totalLiabilities + $totalEquity + $currentNetIncome;

        return [
            'assets' => $assets,
            'total_assets' => $totalAssets,
            'liabilities' => $liabilities,
            'total_liabilities' => $totalLiabilities,
            'equity' => $equity,
            'total_equity' => $totalEquity,
            'current_net_income' => $currentNetIncome,
            'total_liabilities_and_equity' => $totalEquityAndLiabilities,
            'is_balanced' => abs($totalAssets - $totalEquityAndLiabilities) < 0.01,
            'date' => $date->format('Y-m-d'),
        ];
    }

    private function getAccountBalances(string $type, ?Carbon $startDate, ?Carbon $endDate, ?string $codePrefix = null): Collection
    {
        $query = ChartOfAccount::where('type', $type);
        
        if ($codePrefix) {
            $query->where('code', 'like', $codePrefix . '%');
        }

        // Get leaf accounts (level 3 usually, or just checked by usage)
        // Or simply get all accounts and calculate their balance
        // Better to get specific active accounts
        $accounts = $query->get();

        return $accounts->map(function ($account) use ($startDate, $endDate) {
            $balance = $this->calculateAccountBalance($account->id, $account->nature, $startDate, $endDate);
            
            // Only return accounts with activity or non-zero balance
            if ($balance == 0) return null;

            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name_ar,
                'balance' => $balance,
            ];
        })->filter()->values();
    }

    private function calculateAccountBalance($accountId, $nature, $startDate, $endDate): float
    {
        $query = JournalEntryLine::where('account_id', $accountId)
            ->whereHas('entry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted');
                if ($startDate) $q->whereDate('entry_date', '>=', $startDate);
                if ($endDate) $q->whereDate('entry_date', '<=', $endDate);
            });

        $debits = $query->sum('debit');
        $credits = $query->sum('credit');

        if ($nature === 'debit') {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }
}
