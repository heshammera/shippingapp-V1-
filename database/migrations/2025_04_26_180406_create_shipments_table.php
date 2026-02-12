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
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->foreignId('shipping_company_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_agent_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('status_id')->constrained('shipment_statuses')->onDelete('restrict');
            $table->date('shipping_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('return_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('agent_notes')->nullable();
            $table->integer('edit_count')->default(0);
            $table->timestamps();
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
