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
        Schema::table('users', function (Blueprint $table) {
            // 1. إضافة تاريخ الانتهاء
            if (!Schema::hasColumn('users', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('is_active');
            }
            
            // 2. ربط المندوب بشركة الشحن
            if (!Schema::hasColumn('users', 'shipping_company_id')) {
                $table->foreignId('shipping_company_id')
                    ->nullable()
                    ->after('expires_at')
                    ->constrained('shipping_companies')
                    ->nullOnDelete();
            }
            
            // 3. حذف العمود القديم 'role' (سنستخدم Spatie roles)
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            // 4. حذف role_id إذا كان موجوداً (للتوافق)
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // عكس التغييرات
            if (Schema::hasColumn('users', 'shipping_company_id')) {
                $table->dropForeign(['shipping_company_id']);
                $table->dropColumn('shipping_company_id');
            }
            
            if (Schema::hasColumn('users', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            
            // إعادة role column (اختياري)
            // $table->string('role')->default('viewer')->after('email');
        });
    }
};
