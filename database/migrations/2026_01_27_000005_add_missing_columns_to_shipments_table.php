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
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'delivery_agent_name')) {
                $table->string('delivery_agent_name')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'is_printed')) {
                $table->boolean('is_printed')->default(false);
            }
            if (!Schema::hasColumn('shipments', 'print_date')) {
                $table->timestamp('print_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['delivery_agent_name', 'is_printed', 'print_date']);
        });
    }
};
