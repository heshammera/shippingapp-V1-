<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        // 1) app_settings
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('default_shipping_company_id')->nullable()->constrained('shipping_companies')->nullOnDelete();
                $table->foreignId('delivered_status_id')->nullable()->constrained('shipment_statuses')->nullOnDelete();
                $table->foreignId('returned_status_id')->nullable()->constrained('shipment_statuses')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 2) shipment_statuses أعلام وترتيب/لون (تعالج أخطاء sort_order, color)
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

        // 3) shipments توثيق + أعلام v2
        if (Schema::hasTable('shipments')) {
            Schema::table('shipments', function (Blueprint $table) {
                if (!Schema::hasColumn('shipments','delivered_at')) $table->timestamp('delivered_at')->nullable()->after('updated_at');
                if (!Schema::hasColumn('shipments','returned_at'))  $table->timestamp('returned_at')->nullable()->after('delivered_at');

                if (!Schema::hasColumn('shipments','inventory_reserved_at')) $table->timestamp('inventory_reserved_at')->nullable()->after('returned_at');
                if (!Schema::hasColumn('shipments','inventory_released_at')) $table->timestamp('inventory_released_at')->nullable()->after('inventory_reserved_at');
                if (!Schema::hasColumn('shipments','inventory_returned_at')) $table->timestamp('inventory_returned_at')->nullable()->after('inventory_released_at');
            });
        }

        // 4) products تأكيد وجود stock
        if (Schema::hasTable('products') && !Schema::hasColumn('products','stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('stock')->default(0)->after('price');
            });
        }

        // 5) shipping_companies سلوك يؤثر المخزون ولا لأ
        if (Schema::hasTable('shipping_companies') && !Schema::hasColumn('shipping_companies','affects_inventory')) {
            Schema::table('shipping_companies', function (Blueprint $table) {
                $table->boolean('affects_inventory')->default(true)->after('name');
            });
        }

        // 6) جدول حركات المخزون
        if (!Schema::hasTable('inventory_movements')) {
            Schema::create('inventory_movements', function (Blueprint $t) {
                $t->id();
                $t->foreignId('product_id')->constrained()->cascadeOnDelete();
                $t->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
                $t->integer('qty_change'); // + أو -
                $t->string('reason');      // reserve_on_company_exit / release_on_company_back / return_on_status / backfill...
                $t->json('meta')->nullable();
                $t->timestamps();

                $t->index(['product_id','shipment_id','reason']);
            });
        }
    }

    public function down(): void {
        // لا حذف لتجنّب فقدان بيانات
    }
};
