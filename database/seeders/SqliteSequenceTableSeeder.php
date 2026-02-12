<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SqliteSequenceTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sqlite_sequence')->delete();
        
        \DB::table('sqlite_sequence')->insert(array (
            0 => 
            array (
                'name' => 'migrations',
                'seq' => 33,
            ),
            1 => 
            array (
                'name' => 'shipping_companies',
                'seq' => 4,
            ),
            2 => 
            array (
                'name' => 'shipment_statuses',
                'seq' => 36,
            ),
            3 => 
            array (
                'name' => 'role_permission',
                'seq' => 30,
            ),
            4 => 
            array (
                'name' => 'users',
                'seq' => 10,
            ),
            5 => 
            array (
                'name' => 'products',
                'seq' => 4,
            ),
            6 => 
            array (
                'name' => 'collections',
                'seq' => 3,
            ),
            7 => 
            array (
                'name' => 'expenses',
                'seq' => 1,
            ),
            8 => 
            array (
                'name' => 'settings',
                'seq' => 15,
            ),
            9 => 
            array (
                'name' => 'shipments',
                'seq' => 18,
            ),
            10 => 
            array (
                'name' => 'roles',
                'seq' => 4,
            ),
            11 => 
            array (
                'name' => 'permissions',
                'seq' => 21,
            ),
            12 => 
            array (
                'name' => 'migrations',
                'seq' => 33,
            ),
            13 => 
            array (
                'name' => 'shipping_companies',
                'seq' => 4,
            ),
            14 => 
            array (
                'name' => 'shipment_statuses',
                'seq' => 36,
            ),
            15 => 
            array (
                'name' => 'role_permission',
                'seq' => 30,
            ),
            16 => 
            array (
                'name' => 'users',
                'seq' => 10,
            ),
            17 => 
            array (
                'name' => 'products',
                'seq' => 4,
            ),
            18 => 
            array (
                'name' => 'collections',
                'seq' => 3,
            ),
            19 => 
            array (
                'name' => 'expenses',
                'seq' => 1,
            ),
            20 => 
            array (
                'name' => 'settings',
                'seq' => 15,
            ),
            21 => 
            array (
                'name' => 'shipments',
                'seq' => 18,
            ),
            22 => 
            array (
                'name' => 'roles',
                'seq' => 4,
            ),
            23 => 
            array (
                'name' => 'permissions',
                'seq' => 21,
            ),
        ));
        
        
    }
}