<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShippingCompaniesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shipping_companies')->delete();
        
        \DB::table('shipping_companies')->insert(array (
            0 => 
            array (
                'id' => 3,
            'name' => 'عثمان (قاهره)',
                'contact_person' => '13456',
                'phone' => '12346789',
                'email' => 'hesham.merhhha@gmail.com',
                'address' => NULL,
                'is_active' => 1,
                'created_at' => '2025-04-27 17:18:50',
                'updated_at' => '2025-06-13 20:35:58',
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'مرسال',
                'contact_person' => 'هاتف',
                'phone' => '0987654321234567',
                'email' => 'conkdom982@gmail.com',
                'address' => 'تلرلا',
                'is_active' => 1,
                'created_at' => '2025-05-01 19:45:31',
                'updated_at' => '2025-07-13 21:34:44',
            ),
            2 => 
            array (
                'id' => 6,
                'name' => 'غير محدد',
                'contact_person' => 'غير محدد',
                'phone' => '1234567890',
                'email' => 'hesham.mera@gmail.com',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22
El mohandseen towers Num 5 flat Num 22',
                'is_active' => 1,
                'created_at' => '2025-05-10 13:11:13',
                'updated_at' => '2025-05-10 13:11:13',
            ),
            3 => 
            array (
                'id' => 7,
                'name' => 'المكتب',
                'contact_person' => 'محلي',
                'phone' => '01212454',
                'email' => 'hesham.mera@gmail.com',
                'address' => 'badrashin, giza',
                'is_active' => 1,
                'created_at' => '2025-05-13 16:58:23',
                'updated_at' => '2025-05-13 16:58:23',
            ),
            4 => 
            array (
                'id' => 9,
                'name' => 'حمدي المختار',
                'contact_person' => 'هاتف',
                'phone' => '015854543',
                'email' => 'hamdy@g.c',
                'address' => 'البدرشين',
                'is_active' => 1,
                'created_at' => '2025-05-29 15:12:21',
                'updated_at' => '2025-05-29 15:12:21',
            ),
            5 => 
            array (
                'id' => 10,
            'name' => 'عثمان (محافظات)',
                'contact_person' => 'aِhmed ashraf ali abd elmawgoud ahmed',
                'phone' => '1124116368',
                'email' => 'ahmedquran2609@gmail.com',
                'address' => 'badrashin, giza',
                'is_active' => 1,
                'created_at' => '2025-06-13 20:36:21',
                'updated_at' => '2025-06-13 20:36:21',
            ),
        ));
        
        
    }
}