<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
       $permissions = [
    ['name' => 'shipments.view', 'description' => 'عرض الشحنات'],
    ['name' => 'shipments.create', 'description' => 'إضافة شحنة'],
    ['name' => 'shipments.edit', 'description' => 'تعديل شحنة'],
    ['name' => 'shipments.delete', 'description' => 'حذف شحنة'],
    ['name' => 'shipments.export', 'description' => 'تصدير الشحنات'],
    ['name' => 'shipments.track', 'description' => 'تتبع الشحنات'],

    ['name' => 'users.view', 'description' => 'عرض المستخدمين'],
    ['name' => 'users.create', 'description' => 'إضافة مستخدم'],
    ['name' => 'users.edit', 'description' => 'تعديل مستخدم'],
    ['name' => 'users.delete', 'description' => 'حذف مستخدم'],
    ['name' => 'users.assign_roles', 'description' => 'تعيين أدوار'],

    ['name' => 'invoices.view', 'description' => 'عرض الفواتير'],
    ['name' => 'invoices.print', 'description' => 'طباعة الفواتير'],
    ['name' => 'invoices.export', 'description' => 'تصدير الفواتير'],
    ['name' => 'invoices.create_manual', 'description' => 'إنشاء فاتورة يدويًا'],

    ['name' => 'settings.general', 'description' => 'تعديل الإعدادات العامة'],
    ['name' => 'settings.notifications', 'description' => 'إدارة التنبيهات'],
    ['name' => 'settings.roles_permissions', 'description' => 'إدارة الأدوار والصلاحيات'],

    ['name' => 'shipping_companies.view', 'description' => 'عرض شركات الشحن'],
    ['name' => 'shipping_companies.manage', 'description' => 'إدارة شركات الشحن'],
];

foreach ($permissions as $perm) {
    \Spatie\Permission\Models\Permission::firstOrCreate(
        ['name' => $perm['name']],
        ['description' => $perm['description']]
    );
}

}

}