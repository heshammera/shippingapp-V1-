<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'shipments.view',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:21:14',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'عرض الشحنات',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'shipments.create',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:30:57',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إضافة شحنة',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'shipments.edit',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:30:57',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تعديل شحنة',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'shipments.delete',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:30:57',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => '',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'invoices.view',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:30:57',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => '',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'users.manage',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:30:57',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إدارة المستخدمين',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'shipments.export',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تصدير الشحنات',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'shipments.track',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تتبع الشحنات',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'users.view',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'عرض المستخدمين',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'users.create',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إضافة مستخدم',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'users.edit',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تعديل مستخدم',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'users.delete',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'حذف مستخدم',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'users.assign_roles',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تعيين أدوار',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'invoices.print',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'طباعة الفواتير',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'invoices.export',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تصدير الفواتير',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'invoices.create_manual',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إنشاء فاتورة يدويًا',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'settings.general',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'تعديل الإعدادات العامة',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'settings.notifications',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إدارة التنبيهات',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'settings.roles_permissions',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إدارة الأدوار والصلاحيات',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'shipping_companies.view',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'عرض شركات الشحن',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'shipping_companies.manage',
                'guard_name' => 'web',
                'created_at' => '2025-05-01 07:38:09',
                'updated_at' => '2025-05-01 07:57:39',
                'description' => 'إدارة شركات الشحن',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'view_collections',
                'guard_name' => 'web',
                'created_at' => '2025-05-05 23:03:48',
                'updated_at' => '2025-05-05 23:03:48',
                'description' => 'عرض التحصيلات',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'view_reports',
                'guard_name' => 'web',
                'created_at' => '2025-05-05 23:03:48',
                'updated_at' => '2025-05-05 23:03:48',
                'description' => 'عرض التقارير',
            ),

        ));
        
        
    }
}