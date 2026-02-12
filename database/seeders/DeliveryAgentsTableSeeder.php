<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeliveryAgentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('delivery_agents')->delete();
        
        \DB::table('delivery_agents')->insert(array (
            0 => 
            array (
                'id' => 1007,
                'name' => 'محمود خليفة',
                'phone' => '01119199792',
                'email' => 'mahmoud@g.com',
                'address' => 'البدرشين',
                'national_id' => '29807122100057',
                'user_id' => NULL,
                'shipping_company_id' => 7,
                'max_edit_count' => 1,
                'is_active' => 1,
                'notes' => NULL,
                'created_at' => '2025-05-16 19:48:14',
                'updated_at' => '2025-05-16 19:48:14',
            ),
            1 => 
            array (
                'id' => 1008,
                'name' => 'ابو روضة',
                'phone' => '01151582070',
                'email' => 'mahmoud@gv.com',
                'address' => 'البدرشين',
                'national_id' => '29807122100057',
                'user_id' => NULL,
                'shipping_company_id' => 7,
                'max_edit_count' => 1,
                'is_active' => 1,
                'notes' => NULL,
                'created_at' => '2025-05-16 19:56:48',
                'updated_at' => '2025-05-16 19:56:48',
            ),
        ));
        
        
    }
}