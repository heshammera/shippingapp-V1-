<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique(); // JE-2024-0001
            $table->date('entry_date');
            $table->text('description')->nullable();
            
            $table->enum('type', ['manual', 'automatic', 'opening_balance'])->default('manual');
            
            // Reference to other records (Polymorphic can be good, but simple is better for now)
            $table->nullableMorphs('reference'); // invoice_id, etc.
            
            $table->enum('status', ['draft', 'posted'])->default('draft');
            
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('entry_date');
            $table->index('type');
        });
        
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            
            $table->text('description')->nullable(); // Line description
            
            $table->timestamps();
            
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
    }
};
