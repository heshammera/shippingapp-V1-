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
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('movement_type', ['purchase', 'adjustment', 'reserve', 'release', 'deduct', 'return', 'transfer'])->comment('نوع الحركة');
            $table->integer('quantity_change')->comment('التغيير في الكمية (+/-)');
            $table->integer('quantity_before')->comment('الكمية قبل');
            $table->integer('quantity_after')->comment('الكمية بعد');
            $table->text('reason')->nullable()->comment('سبب الحركة');
            $table->string('reference_number', 50)->nullable()->comment('رقم مرجعي (مثل رقم فاتورة الشراء)');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('من أجرى العملية');
            $table->string('ip_address', 45)->nullable()->comment('IP Address للأمان');
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for reporting
            $table->index(['variant_id', 'created_at']);
            $table->index(['movement_type', 'created_at']);
            $table->index('shipment_id');
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
