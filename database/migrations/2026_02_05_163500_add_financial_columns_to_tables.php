<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_statuses', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('name'); // e.g., 'delivered', 'returned'
        });

        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shipment_statuses', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
