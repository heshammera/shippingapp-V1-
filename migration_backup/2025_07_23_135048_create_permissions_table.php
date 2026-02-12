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
        Schema::create('permissions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 26)->nullable();
            $table->string('guard_name', 3)->nullable();
            $table->string('created_at', 19)->nullable();
            $table->string('updated_at', 19)->nullable();
            $table->string('description', 24)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
