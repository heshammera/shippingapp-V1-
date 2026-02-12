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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->string('id', 10)->nullable();
            $table->string('tokenable_type', 10)->nullable();
            $table->string('tokenable_id', 10)->nullable();
            $table->string('name', 10)->nullable();
            $table->string('token', 10)->nullable();
            $table->string('abilities', 10)->nullable();
            $table->string('last_used_at', 10)->nullable();
            $table->string('expires_at', 10)->nullable();
            $table->string('created_at', 10)->nullable();
            $table->string('updated_at', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
