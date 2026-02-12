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
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->string('id', 10)->nullable();
            $table->string('uuid', 10)->nullable();
            $table->string('connection', 10)->nullable();
            $table->string('queue', 10)->nullable();
            $table->string('payload', 10)->nullable();
            $table->string('exception', 10)->nullable();
            $table->string('failed_at', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
