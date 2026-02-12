<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::create('shipment_product', function (Blueprint $table) {
        $table->id();
        // تأكد من أن الحقل shipment_id هو unsignedBigInteger
        $table->unsignedBigInteger('shipment_id');
        $table->unsignedBigInteger('product_id');
        $table->string('color');
        $table->string('size');
        $table->integer('quantity');
        $table->decimal('price', 8, 2);
        $table->timestamps();

        // إضافة foreign key على حقل shipment_id
        $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
        // إضافة foreign key على حقل product_id
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
