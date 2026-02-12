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
Schema::create('stock_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // لازم يطابق products.id (BIGINT)
    $table->string('color');
    $table->string('size');
    $table->integer('delta');
    $table->integer('before_qty');
    $table->integer('after_qty');
    $table->string('reason')->nullable();
    $table->string('reference_type')->nullable();
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->timestamps();

    // FK handled by constrained()
    // $table->foreign('product_id', 'stock_movements_product_id_fk')
    //       ->references('id')->on('products')
    //       ->onDelete('cascade');
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
