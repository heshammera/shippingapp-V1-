<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShippingRatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shipping_rates')->delete();
        
        \DB::table('shipping_rates')->insert(array (
            0 => 
            array (
                'id' => 55,
                'governorate' => 'القاهرة',
                'price' => '50.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 56,
                'governorate' => 'الجيزة',
                'price' => '50.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 57,
                'governorate' => 'القليوبية',
                'price' => '50.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 58,
                'governorate' => 'أسيوط',
                'price' => '75.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 59,
                'governorate' => 'الأقصر',
                'price' => '75.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 60,
                'governorate' => 'أسوان',
                'price' => '75.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 61,
                'governorate' => 'سوهاج',
                'price' => '75.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 62,
                'governorate' => 'الإسكندرية',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 63,
                'governorate' => 'البحيرة',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 64,
                'governorate' => 'كفر الشيخ',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 65,
                'governorate' => 'المنوفية',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 66,
                'governorate' => 'الدقهلية',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 67,
                'governorate' => 'الغربية',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 68,
                'governorate' => 'الشرقية',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 69,
                'governorate' => 'دمياط',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 70,
                'governorate' => 'بورسعيد',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 71,
                'governorate' => 'الإسماعيلية',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 72,
                'governorate' => 'السويس',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 73,
                'governorate' => 'بني سويف',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 74,
                'governorate' => 'المنيا',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 75,
                'governorate' => 'الفيوم',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 76,
                'governorate' => 'قنا',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 77,
                'governorate' => 'مطروح',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 78,
                'governorate' => 'شمال سيناء',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 79,
                'governorate' => 'جنوب سيناء',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 80,
                'governorate' => 'الوادي الجديد',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 81,
                'governorate' => 'البحر الأحمر',
                'price' => '60.00',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}