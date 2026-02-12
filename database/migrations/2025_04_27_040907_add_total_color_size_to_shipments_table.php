<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('color')->nullable()->after('product_name');
            $table->string('size')->nullable()->after('color');
            $table->decimal('total_amount', 10, 2)->nullable()->after('selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['color', 'size', 'total_amount']);
        });
    }
};
