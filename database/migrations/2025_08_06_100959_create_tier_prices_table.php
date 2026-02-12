<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tier_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->integer('min_qty');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_prices');
    }
};
