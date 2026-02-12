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
        Schema::create('shipment_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('shipment_id')->index('shipment_product_shipment_id_foreign');
            $table->unsignedInteger('product_id')->index('shipment_product_product_id_foreign');
            $table->string('color');
            $table->string('size');
            $table->integer('quantity');
            $table->decimal('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_product');
    }
};
