<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShipmentStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shipment_statuses')->delete();
        
        \DB::table('shipment_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'sort_order' => 0,
                'name' => 'تم التوصيل',
                'color' => 'table-success',
                'is_default' => 0,
                'created_at' => '2025-04-26 22:09:05',
                'updated_at' => '2025-05-10 15:51:51',
                'row_color' => '',
            ),
            1 => 
            array (
                'id' => 2,
                'sort_order' => 0,
                'name' => 'مرتجع',
                'color' => 'table-danger',
                'is_default' => 0,
                'created_at' => '2025-04-26 22:09:05',
                'updated_at' => '2025-05-10 15:51:45',
                'row_color' => '',
            ),
            2 => 
            array (
                'id' => 3,
                'sort_order' => 0,
                'name' => 'عهدة أو تدوير',
                'color' => 'table-primary',
                'is_default' => 0,
                'created_at' => '2025-04-26 22:09:05',
                'updated_at' => '2025-05-10 15:43:47',
                'row_color' => '',
            ),
            3 => 
            array (
                'id' => 37,
                'sort_order' => 0,
                'name' => 'غير محدد',
                'color' => 'table-light',
                'is_default' => 0,
                'created_at' => '2025-05-06 04:53:26',
                'updated_at' => '2025-05-10 15:51:36',
                'row_color' => NULL,
            ),
            4 => 
            array (
                'id' => 39,
                'sort_order' => 0,
                'name' => 'مجاش',
                'color' => 'table-purple',
                'is_default' => 0,
                'created_at' => '2025-05-08 22:46:24',
                'updated_at' => '2025-05-26 20:12:26',
                'row_color' => NULL,
            ),
            5 => 
            array (
                'id' => 40,
                'sort_order' => 0,
                'name' => 'ملغي',
                'color' => 'table-dark',
                'is_default' => 0,
                'created_at' => '2025-05-10 14:08:45',
                'updated_at' => '2025-05-21 15:47:53',
                'row_color' => NULL,
            ),
            6 => 
            array (
                'id' => 42,
                'sort_order' => 0,
            'name' => 'تم التوصيل (كاش)',
                'color' => 'table-success',
                'is_default' => 0,
                'created_at' => '2025-05-26 19:37:24',
                'updated_at' => '2025-07-20 20:43:18',
                'row_color' => NULL,
            ),
            7 => 
            array (
                'id' => 45,
                'sort_order' => 0,
                'name' => 'تبديل بدون دفع',
                'color' => 'table-orange',
                'is_default' => 0,
                'created_at' => '2025-05-26 19:50:39',
                'updated_at' => '2025-05-26 20:10:26',
                'row_color' => NULL,
            ),
            8 => 
            array (
                'id' => 46,
                'sort_order' => 0,
                'name' => 'تم التأكيد',
                'color' => 'table-info',
                'is_default' => 0,
                'created_at' => '2025-06-13 18:43:05',
                'updated_at' => '2025-06-13 18:43:05',
                'row_color' => NULL,
            ),
            9 => 
            array (
                'id' => 47,
                'sort_order' => 0,
                'name' => 'لا يرد',
                'color' => 'table-orange',
                'is_default' => 0,
                'created_at' => '2025-06-13 18:43:25',
                'updated_at' => '2025-06-13 18:43:25',
                'row_color' => NULL,
            ),
        ));
        
        
    }
}