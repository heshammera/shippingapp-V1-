<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('shipment_statuses', function (Blueprint $table) {
        $table->string('row_color')->nullable()->after('name'); // مثال: 'table-success'
    });
}

public function down()
{
    Schema::table('shipment_statuses', function (Blueprint $table) {
        $table->dropColumn('row_color');
    });
}

};
