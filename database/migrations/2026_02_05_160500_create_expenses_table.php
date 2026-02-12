<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // EXP-2024-0001
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            
            // Financial Classification
            $table->foreignId('expense_account_id')->constrained('chart_of_accounts'); // Where to debit (Expense Account)
            $table->foreignId('paid_via_account_id')->nullable()->constrained('chart_of_accounts'); // Where to credit (Cash/Bank) - Null if not paid yet (Accounts Payable)
            
            // Workflow
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
            
            // Evidence
            $table->string('receipt_image')->nullable();
            
            // Tracking
            $table->foreignId('user_id')->constrained('users'); // Requester
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
