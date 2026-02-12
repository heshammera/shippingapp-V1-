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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('email_verified_at', 10)->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token', 10)->nullable();
            $table->string('created_at', 19)->nullable();
            $table->string('updated_at', 19)->nullable();
            $table->string('role', 100)->nullable();
            $table->string('role_id', 2)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address', 93)->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->integer('is_active')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('theme_color', 7)->nullable()->default('#2C3E50');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
