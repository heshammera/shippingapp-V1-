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
        Schema::create('collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shipping_company_id')->nullable();
            $table->unsignedBigInteger('delivery_agent_id')->nullable()->index('collections_delivery_agent_id_foreign');
            $table->integer('amount')->nullable();
            $table->string('collection_date', 19)->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->string('created_at', 19)->nullable();
            $table->string('updated_at', 19)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
