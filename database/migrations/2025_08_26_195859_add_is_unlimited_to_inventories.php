<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories','is_unlimited')) {
                $table->boolean('is_unlimited')->default(false)->after('quantity');
            }
        });
    }

    public function down(): void {
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories','is_unlimited')) {
                $table->dropColumn('is_unlimited');
            }
        });
    }
};
