<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_companies', function (Blueprint $table) {
            $table->string('integration_type')->default('internal')->after('is_active'); // internal, aramex, dhl
            $table->json('api_settings')->nullable()->after('integration_type');
            $table->boolean('integration_enabled')->default(false)->after('api_settings');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('external_tracking_number')->nullable()->after('tracking_number');
            $table->string('external_reference')->nullable()->after('external_tracking_number');
            $table->json('carrier_data')->nullable()->after('external_reference'); // Store API response snippets
        });
    }

    public function down(): void
    {
        Schema::table('shipping_companies', function (Blueprint $table) {
            $table->dropColumn(['integration_type', 'api_settings', 'integration_enabled']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['external_tracking_number', 'external_reference', 'carrier_data']);
        });
    }
};
