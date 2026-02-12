<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('shipment_statuses')) {
            Schema::table('shipment_statuses', function (Blueprint $table) {
                if (!Schema::hasColumn('shipment_statuses','is_delivered_semantic')) {
                    $table->boolean('is_delivered_semantic')->default(false)->after('name');
                }
                if (!Schema::hasColumn('shipment_statuses','is_returned_semantic')) {
                    $table->boolean('is_returned_semantic')->default(false)->after('is_delivered_semantic');
                }
                if (!Schema::hasColumn('shipment_statuses','sort_order')) {
                    $table->unsignedInteger('sort_order')->default(0)->after('is_returned_semantic');
                }
                if (!Schema::hasColumn('shipment_statuses','color')) {
                    $table->string('color')->nullable()->after('sort_order');
                }
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('shipment_statuses')) {
            Schema::table('shipment_statuses', function (Blueprint $table) {
                foreach (['color','sort_order','is_returned_semantic','is_delivered_semantic'] as $col) {
                    if (Schema::hasColumn('shipment_statuses',$col)) $table->dropColumn($col);
                }
            });
        }
    }
};
