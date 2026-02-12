<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id(); // BIGINT للجدول نفسه عادي
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // يطابق products.id (BIGINT UNSIGNED)
            $table->string('color', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('low_stock_alert')->default(0);
            $table->timestamps();

            // FK constraint is already handled by constrained() above
            // $table->foreign('product_id', 'inventories_product_id_fk')
            //       ->references('id')->on('products')
            //       ->onDelete('cascade');

            // منع تكرار نفس (المنتج/اللون/المقاس)
            $table->unique(['product_id', 'color', 'size'], 'inv_product_color_size_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

