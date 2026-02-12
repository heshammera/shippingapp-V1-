<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MigrationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('migrations')->delete();
        
        \DB::table('migrations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'migration' => '2014_10_12_000000_create_users_table',
                'batch' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'migration' => '2014_10_12_100000_create_password_reset_tokens_table',
                'batch' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'migration' => '2019_08_19_000000_create_failed_jobs_table',
                'batch' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'migration' => '2019_12_14_000001_create_personal_access_tokens_table',
                'batch' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'migration' => '2025_04_26_180405_create_delivery_agents_table',
                'batch' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'migration' => '2025_04_26_180405_create_permissions_table',
                'batch' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'migration' => '2025_04_26_180405_create_role_permission_table',
                'batch' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'migration' => '2025_04_26_180405_create_roles_table',
                'batch' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'migration' => '2025_04_26_180405_create_shipping_companies_table',
                'batch' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'migration' => '2025_04_26_180406_create_collections_table',
                'batch' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'migration' => '2025_04_26_180406_create_expenses_table',
                'batch' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'migration' => '2025_04_26_180406_create_settings_table',
                'batch' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'migration' => '2025_04_26_180406_create_shipment_statuses_table',
                'batch' => 1,
            ),
            13 => 
            array (
                'id' => 15,
                'migration' => '2025_04_26_180406_create_shipments_table',
                'batch' => 2,
            ),
            14 => 
            array (
                'id' => 16,
                'migration' => '2025_04_27_025811_create_products_table',
                'batch' => 3,
            ),
            15 => 
            array (
                'id' => 17,
                'migration' => '2025_04_27_040907_add_total_color_size_to_shipments_table',
                'batch' => 4,
            ),
            16 => 
            array (
                'id' => 18,
                'migration' => '2025_04_29_121241_add_governorate_to_shipments_table',
                'batch' => 5,
            ),
            17 => 
            array (
                'id' => 19,
                'migration' => '2025_04_29_121826_add_shipping_price_to_shipments_table',
                'batch' => 6,
            ),
            18 => 
            array (
                'id' => 20,
                'migration' => '2025_04_29_122224_add_product_id_to_shipments_table',
                'batch' => 7,
            ),
            19 => 
            array (
                'id' => 21,
                'migration' => '2025_04_29_150130_add_shipping_company_to_shipments_table',
                'batch' => 8,
            ),
            20 => 
            array (
                'id' => 22,
                'migration' => '2025_04_29_161449_modify_shipping_company_id_nullable',
                'batch' => 9,
            ),
            21 => 
            array (
                'id' => 23,
                'migration' => '2025_04_30_015920_add_row_color_to_shipment_statuses_table',
                'batch' => 10,
            ),
            22 => 
            array (
                'id' => 24,
                'migration' => '2025_05_01_025742_add_role_id_to_users_table',
                'batch' => 11,
            ),
            23 => 
            array (
                'id' => 25,
                'migration' => '2025_05_01_031205_add_phone_address_is_active_to_users_table',
                'batch' => 12,
            ),
            24 => 
            array (
                'id' => 26,
                'migration' => '2025_05_01_031540_create_permission_role_table',
                'batch' => 13,
            ),
            25 => 
            array (
                'id' => 27,
                'migration' => '2025_04_26_182939_create_delivery_agents_table',
                'batch' => 14,
            ),
            26 => 
            array (
                'id' => 28,
                'migration' => '2025_04_28_085733_add_missing_fields_to_shipments_table',
                'batch' => 14,
            ),
            27 => 
            array (
                'id' => 29,
                'migration' => '2025_04_28_090043_2025_04_28_085733_add_missing_fields_to_shipments_table',
                'batch' => 14,
            ),
            28 => 
            array (
                'id' => 30,
                'migration' => '2025_05_01_050759_create_permission_tables',
                'batch' => 15,
            ),
            29 => 
            array (
                'id' => 31,
                'migration' => '2025_05_01_070809_add_expires_at_to_users_table',
                'batch' => 16,
            ),
            30 => 
            array (
                'id' => 32,
                'migration' => '2025_05_01_073501_add_description_to_permissions_table',
                'batch' => 17,
            ),
            31 => 
            array (
                'id' => 33,
                'migration' => '2025_05_01_081543_add_description_to_roles_table',
                'batch' => 18,
            ),
            32 => 
            array (
                'id' => NULL,
                'migration' => '2025_05_06_054629_create_activity_log_table',
                'batch' => 19,
            ),
            33 => 
            array (
                'id' => NULL,
                'migration' => '2025_05_06_054630_add_event_column_to_activity_log_table',
                'batch' => 20,
            ),
            34 => 
            array (
                'id' => NULL,
                'migration' => '2025_05_06_054631_add_batch_uuid_column_to_activity_log_table',
                'batch' => 21,
            ),
            35 => 
            array (
                'id' => 1,
                'migration' => '2014_10_12_000000_create_users_table',
                'batch' => 1,
            ),
            36 => 
            array (
                'id' => 2,
                'migration' => '2014_10_12_100000_create_password_reset_tokens_table',
                'batch' => 1,
            ),
            37 => 
            array (
                'id' => 3,
                'migration' => '2019_08_19_000000_create_failed_jobs_table',
                'batch' => 1,
            ),
            38 => 
            array (
                'id' => 4,
                'migration' => '2019_12_14_000001_create_personal_access_tokens_table',
                'batch' => 1,
            ),
            39 => 
            array (
                'id' => 5,
                'migration' => '2025_04_26_180405_create_delivery_agents_table',
                'batch' => 1,
            ),
            40 => 
            array (
                'id' => 6,
                'migration' => '2025_04_26_180405_create_permissions_table',
                'batch' => 1,
            ),
            41 => 
            array (
                'id' => 7,
                'migration' => '2025_04_26_180405_create_role_permission_table',
                'batch' => 1,
            ),
            42 => 
            array (
                'id' => 8,
                'migration' => '2025_04_26_180405_create_roles_table',
                'batch' => 1,
            ),
            43 => 
            array (
                'id' => 9,
                'migration' => '2025_04_26_180405_create_shipping_companies_table',
                'batch' => 1,
            ),
            44 => 
            array (
                'id' => 10,
                'migration' => '2025_04_26_180406_create_collections_table',
                'batch' => 1,
            ),
            45 => 
            array (
                'id' => 11,
                'migration' => '2025_04_26_180406_create_expenses_table',
                'batch' => 1,
            ),
            46 => 
            array (
                'id' => 12,
                'migration' => '2025_04_26_180406_create_settings_table',
                'batch' => 1,
            ),
            47 => 
            array (
                'id' => 13,
                'migration' => '2025_04_26_180406_create_shipment_statuses_table',
                'batch' => 1,
            ),
            48 => 
            array (
                'id' => 15,
                'migration' => '2025_04_26_180406_create_shipments_table',
                'batch' => 2,
            ),
            49 => 
            array (
                'id' => 16,
                'migration' => '2025_04_27_025811_create_products_table',
                'batch' => 3,
            ),
            50 => 
            array (
                'id' => 17,
                'migration' => '2025_04_27_040907_add_total_color_size_to_shipments_table',
                'batch' => 4,
            ),
            51 => 
            array (
                'id' => 18,
                'migration' => '2025_04_29_121241_add_governorate_to_shipments_table',
                'batch' => 5,
            ),
            52 => 
            array (
                'id' => 19,
                'migration' => '2025_04_29_121826_add_shipping_price_to_shipments_table',
                'batch' => 6,
            ),
            53 => 
            array (
                'id' => 20,
                'migration' => '2025_04_29_122224_add_product_id_to_shipments_table',
                'batch' => 7,
            ),
            54 => 
            array (
                'id' => 21,
                'migration' => '2025_04_29_150130_add_shipping_company_to_shipments_table',
                'batch' => 8,
            ),
            55 => 
            array (
                'id' => 22,
                'migration' => '2025_04_29_161449_modify_shipping_company_id_nullable',
                'batch' => 9,
            ),
            56 => 
            array (
                'id' => 23,
                'migration' => '2025_04_30_015920_add_row_color_to_shipment_statuses_table',
                'batch' => 10,
            ),
            57 => 
            array (
                'id' => 24,
                'migration' => '2025_05_01_025742_add_role_id_to_users_table',
                'batch' => 11,
            ),
            58 => 
            array (
                'id' => 25,
                'migration' => '2025_05_01_031205_add_phone_address_is_active_to_users_table',
                'batch' => 12,
            ),
            59 => 
            array (
                'id' => 26,
                'migration' => '2025_05_01_031540_create_permission_role_table',
                'batch' => 13,
            ),
            60 => 
            array (
                'id' => 27,
                'migration' => '2025_04_26_182939_create_delivery_agents_table',
                'batch' => 14,
            ),
            61 => 
            array (
                'id' => 28,
                'migration' => '2025_04_28_085733_add_missing_fields_to_shipments_table',
                'batch' => 14,
            ),
            62 => 
            array (
                'id' => 29,
                'migration' => '2025_04_28_090043_2025_04_28_085733_add_missing_fields_to_shipments_table',
                'batch' => 14,
            ),
            63 => 
            array (
                'id' => 30,
                'migration' => '2025_05_01_050759_create_permission_tables',
                'batch' => 15,
            ),
            64 => 
            array (
                'id' => 31,
                'migration' => '2025_05_01_070809_add_expires_at_to_users_table',
                'batch' => 16,
            ),
            65 => 
            array (
                'id' => 32,
                'migration' => '2025_05_01_073501_add_description_to_permissions_table',
                'batch' => 17,
            ),
            66 => 
            array (
                'id' => 33,
                'migration' => '2025_05_01_081543_add_description_to_roles_table',
                'batch' => 18,
            ),
            67 => 
            array (
                'id' => NULL,
                'migration' => '2025_05_06_054629_create_activity_log_table',
                'batch' => 19,
            ),
            68 => 
            array (
                'id' => NULL,
                'migration' => '2025_05_06_054630_add_event_column_to_activity_log_table',
                'batch' => 20,
            ),
            69 => 
            array (
                'id' => NULL,
                'migration' => '2025_05_06_054631_add_batch_uuid_column_to_activity_log_table',
                'batch' => 21,
            ),
            70 => 
            array (
                'id' => NULL,
                'migration' => '2025_06_29_174925_create_imported_shipments_table',
                'batch' => 22,
            ),
            71 => 
            array (
                'id' => NULL,
                'migration' => '2025_07_02_171901_create_product_prices_table',
                'batch' => 23,
            ),
        ));
        
        
    }
}