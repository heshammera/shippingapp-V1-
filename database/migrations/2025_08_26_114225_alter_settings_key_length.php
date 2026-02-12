<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('settings', function (Blueprint $table) {
            // كبر حجم العمود key
            $table->string('key', 191)->change();
        });
    }

    public function down(): void {
        Schema::table('settings', function (Blueprint $table) {
            // لو عايز ترجع زي ما كان (خلي الحجم زي اللي عندك قبل التعديل)
            $table->string('key', 50)->change();
        });
    }
};
