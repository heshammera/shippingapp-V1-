<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'viewer',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 06:51:37',
                'updated_at' => '2025-05-01 09:28:09',
                'description' => 'مشاهد فقط',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'delivery_agent',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 06:51:37',
                'updated_at' => '2025-05-01 09:28:09',
                'description' => 'مندوب',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'moderator',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 08:30:44',
                'updated_at' => '2025-05-01 09:28:09',
                'description' => 'مودريتور',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 08:57:43',
                'updated_at' => '2025-05-01 09:28:09',
                'description' => 'ادمن',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'accountant',
                'guard_name' => 'web',
                'created_at' => '2025-05-05 23:03:59',
                'updated_at' => '2025-05-05 23:03:59',
                'description' => 'محاسب',
            ),
        ));
        
        
    }
}