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
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 50)->nullable()->unique()->after('id');
            $table->text('description')->nullable()->after('name');
            $table->boolean('track_inventory')->default(true)->after('cost_price');
            $table->unsignedBigInteger('category_id')->nullable()->after('track_inventory');
            $table->boolean('is_active')->default(true)->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'description', 'track_inventory', 'category_id', 'is_active']);
        });
    }
};
