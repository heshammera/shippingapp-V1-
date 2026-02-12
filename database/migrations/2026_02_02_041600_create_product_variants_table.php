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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 50)->unique()->comment('رمز فريد للنوع مثل: TSHIRT-RED-L'); 
            $table->string('color', 50)->nullable();
            $table->string('size', 50)->nullable();
            $table->integer('stock_quantity')->default(0)->comment('الكمية الفعلية في المخزن');
            $table->integer('reserved_quantity')->default(0)->comment('المحجوز في شحنات قيد المعالجة');
            $table->integer('low_stock_threshold')->default(5)->comment('حد التنبيه');
            $table->boolean('is_unlimited')->default(false)->comment('مخزون غير محدود؟');
            $table->string('barcode', 50)->nullable()->comment('الباركود للمسح السريع');
            $table->timestamps();
            
            // Index for better query performance
            $table->index(['product_id', 'color', 'size']);
            $table->unique(['product_id', 'color', 'size'], 'variant_product_color_size_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
