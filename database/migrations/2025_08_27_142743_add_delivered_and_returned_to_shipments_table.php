<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('status_id');
            }
            if (!Schema::hasColumn('shipments', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('delivered_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
            if (Schema::hasColumn('shipments', 'returned_at')) {
                $table->dropColumn('returned_at');
            }
        });
    }
};

