<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments','inventory_reserved_at')) {
                $table->timestamp('inventory_reserved_at')->nullable()->after('return_date');
            }
            if (!Schema::hasColumn('shipments','inventory_released_at')) {
                $table->timestamp('inventory_released_at')->nullable()->after('inventory_reserved_at');
            }
        });
    }

    public function down(): void {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments','inventory_reserved_at')) {
                $table->dropColumn('inventory_reserved_at');
            }
            if (Schema::hasColumn('shipments','inventory_released_at')) {
                $table->dropColumn('inventory_released_at');
            }
        });
    }
};
