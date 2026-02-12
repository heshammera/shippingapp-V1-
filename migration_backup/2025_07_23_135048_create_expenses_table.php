<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title')->nullable();
            $table->integer('amount')->nullable();
            $table->string('expense_date', 19)->nullable();
            $table->string('notes', 10)->nullable();
            $table->integer('created_by')->nullable();
            $table->string('created_at', 19)->nullable();
            $table->string('updated_at', 19)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
