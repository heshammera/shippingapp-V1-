<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inventories', function (Blueprint $table) {
            // 1) إزالة الـ FK القديم (بالاسم الصحيح أو بالمصفوفة)
            $table->dropForeign('inventories_product_id_fk');
            
            // 2) تعديل نوع العمود (ليطابق products.id = INT UNSIGNED)
            $table->unsignedInteger('product_id')->change();
            
            // 3) إعادة إنشاء FK جديد
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::table('inventories', function (Blueprint $table) {
            // 1) حذف الـ FK الجديد
            $table->dropForeign(['product_id']);
            
            // 2) تغيير العمود رجوعاً لـ BIGINT UNSIGNED (زي ما كان Laravel بيعمل)
            $table->unsignedBigInteger('product_id')->change();
            
            // 3) إعادة إنشاء FK بالاسم القديم
            $table->foreign('product_id', 'inventories_product_id_fk')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }
};
