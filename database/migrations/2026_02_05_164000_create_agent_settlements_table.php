<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('agent_id')->constrained('delivery_agents');
            $table->decimal('amount', 15, 2);
            $table->date('settlement_date');
            $table->foreignId('receiving_account_id')->constrained('chart_of_accounts'); // Cash/Bank
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Who recorded it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_settlements');
    }
};
