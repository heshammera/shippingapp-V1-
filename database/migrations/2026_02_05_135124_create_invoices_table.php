<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->uuid('uuid')->unique();
            
            // Customer Information
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            
            // Dates
            $table->date('issue_date');
            $table->date('due_date');
            
            // Financial Details
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(14.00); // 14% VAT Egypt
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled', 'overdue'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            
            // QR Code (for internal tracking)
            $table->text('qr_code')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            
            // Tracking
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('invoice_number');
            $table->index('customer_id');
            $table->index('issue_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
