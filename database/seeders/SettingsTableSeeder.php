<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'company_logo',
                'value' => 'logos/Iz1bsnKzcm6kQaE1MBlHQGB40lKPq6px0xajQGfR.png',
                'description' => 'شعار الشركة',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'company_name',
                'value' => 'نسيج ستور',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'company_address',
                'value' => 'شركة الملابس أونلاين',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'company_phone',
                'value' => '01124116368',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'company_email',
                'value' => 'hesham.mera@gmail.com',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'currency',
                'value' => 'جنيه',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'tax_rate',
                'value' => '14',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'default_status_id',
                'value' => '37',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 18:10:50',
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'pagination_limit',
                'value' => '100',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'default_language',
                'value' => 'ar',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:21',
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'time_format',
                'value' => 'h:i A',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            12 => 
            array (
                'id' => 13,
                'key' => 'enable_activity_log',
                'value' => '1',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            13 => 
            array (
                'id' => 14,
                'key' => 'backup_frequency',
                'value' => 'daily',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            14 => 
            array (
                'id' => 15,
                'key' => 'backup_retention',
                'value' => '7',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            15 => 
            array (
                'id' => 1,
                'key' => 'company_logo',
                'value' => 'logos/Iz1bsnKzcm6kQaE1MBlHQGB40lKPq6px0xajQGfR.png',
                'description' => 'شعار الشركة',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            16 => 
            array (
                'id' => 2,
                'key' => 'company_name',
                'value' => 'نسيج ستور',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            17 => 
            array (
                'id' => 3,
                'key' => 'company_address',
                'value' => 'شركة الملابس أونلاين',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            18 => 
            array (
                'id' => 4,
                'key' => 'company_phone',
                'value' => '01124116368',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            19 => 
            array (
                'id' => 5,
                'key' => 'company_email',
                'value' => 'hesham.mera@gmail.com',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            20 => 
            array (
                'id' => 6,
                'key' => 'currency',
                'value' => 'جنيه',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            21 => 
            array (
                'id' => 7,
                'key' => 'tax_rate',
                'value' => '14',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 17:30:26',
            ),
            22 => 
            array (
                'id' => 8,
                'key' => 'default_status_id',
                'value' => '37',
                'description' => '',
                'created_at' => '2025-04-27 17:30:26',
                'updated_at' => '2025-04-27 18:10:50',
            ),
            23 => 
            array (
                'id' => 9,
                'key' => 'pagination_limit',
                'value' => '100',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            24 => 
            array (
                'id' => 10,
                'key' => 'default_language',
                'value' => 'ar',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:21',
            ),
            25 => 
            array (
                'id' => 11,
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            26 => 
            array (
                'id' => 12,
                'key' => 'time_format',
                'value' => 'h:i A',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            27 => 
            array (
                'id' => 13,
                'key' => 'enable_activity_log',
                'value' => '1',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            28 => 
            array (
                'id' => 14,
                'key' => 'backup_frequency',
                'value' => 'daily',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            29 => 
            array (
                'id' => 15,
                'key' => 'backup_retention',
                'value' => '7',
                'description' => NULL,
                'created_at' => '2025-04-27 17:41:15',
                'updated_at' => '2025-04-27 17:41:15',
            ),
            30 => 
            array (
                'id' => NULL,
                'key' => 'google_sheet_id',
                'value' => '1JZJb7ljk3SrBjACfKOqcHaStjjNHx2E9fWGvvY_u8pA',
                'description' => NULL,
                'created_at' => '2025-06-29 23:16:30',
                'updated_at' => '2025-06-29 23:16:30',
            ),
            31 => 
            array (
                'id' => NULL,
                'key' => 'google_sheet_range',
                'value' => 'Sheet1!A2:Z',
                'description' => NULL,
                'created_at' => '2025-06-29 23:16:30',
                'updated_at' => '2025-06-29 23:16:30',
            ),
        ));
        
        
    }
}