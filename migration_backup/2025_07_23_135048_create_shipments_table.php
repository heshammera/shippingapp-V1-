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
        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_company_id')->nullable();
            $table->unsignedBigInteger('delivery_agent_id')->nullable()->index('shipments_delivery_agent_id_foreign');
            $table->string('delivery_agent_name')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('tracking_number', 50)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 25)->nullable();
            $table->text('customer_address')->nullable();
            $table->string('product_name', 100)->nullable();
            $table->text('product_description')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('cost_price', 10)->nullable();
            $table->decimal('selling_price', 10)->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('return_date')->nullable();
            $table->date('print_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('agent_notes')->nullable();
            $table->integer('edit_count')->nullable();
            $table->string('created_at', 19)->nullable();
            $table->string('updated_at', 19)->nullable();
            $table->decimal('total_amount', 10)->nullable();
            $table->text('color')->nullable();
            $table->string('size')->nullable();
            $table->string('governorate', 100)->nullable();
            $table->decimal('shipping_price', 10)->nullable();
            $table->integer('product_id')->nullable();
            $table->string('shipping_company')->nullable();
            $table->boolean('is_printed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
