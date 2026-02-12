<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('product_name');
            }

            if (!Schema::hasColumn('shipments', 'color')) {
                $table->string('color')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('shipments', 'size')) {
                $table->string('size')->nullable()->after('color');
            }

            if (!Schema::hasColumn('shipments', 'governorate')) {
                $table->string('governorate')->nullable()->after('customer_address');
            }

            if (!Schema::hasColumn('shipments', 'shipping_price')) {
                $table->decimal('shipping_price', 10, 2)->nullable()->after('governorate');
            }

            if (!Schema::hasColumn('shipments', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('shipping_price');
            }
        });
    }

    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'product_id')) {
                $table->dropColumn('product_id');
            }

            if (Schema::hasColumn('shipments', 'color')) {
                $table->dropColumn('color');
            }

            if (Schema::hasColumn('shipments', 'size')) {
                $table->dropColumn('size');
            }

            if (Schema::hasColumn('shipments', 'governorate')) {
                $table->dropColumn('governorate');
            }

            if (Schema::hasColumn('shipments', 'shipping_price')) {
                $table->dropColumn('shipping_price');
            }

            if (Schema::hasColumn('shipments', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
