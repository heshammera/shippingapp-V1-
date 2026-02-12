<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === 1. إنشاء جميع الصلاحيات ===
        $permissions = [
            // الشحنات (Shipments)
            'view_shipments',
            'create_shipments',
            'edit_shipments',
            'delete_shipments',
            'export_shipments',
            'print_shipments',
            'assign_shipments', // تخصيص للمندوبين
            
            // المنتجات (Products)
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_stock', // إدارة المخزون
            
            // المستخدمين (Users)
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles', // إدارة الأدوار
            'manage_permissions', // إدارة الصلاحيات
            
            // المحاسبة (Accounting)
            'view_collections',
            'create_collections',
            'edit_collections',
            'delete_collections',
            'view_expenses',
            'create_expenses',
            'edit_expenses',
            'delete_expenses',
            'view_reports',
            
            // شركات الشحن (Shipping Companies)
            'view_shipping_companies',
            'create_shipping_companies',
            'edit_shipping_companies',
            'delete_shipping_companies',
            
            // المندوبين (Delivery Agents)
            'view_delivery_agents',
            'create_delivery_agents',
            'edit_delivery_agents',
            'delete_delivery_agents',
            
            // الإعدادات (Settings)
            'view_settings',
            'edit_settings',
            'manage_backups',
            
            // حالات الشحن (Statuses)
            'view_statuses',
            'create_statuses',
            'edit_statuses',
            'delete_statuses',
            
            // الداشبورد (Dashboard)
            'view_dashboard',
            'view_analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('✅ تم إنشاء ' . count($permissions) . ' صلاحية');

        // === 2. إنشاء الأدوار وتعيين الصلاحيات ===

        // Super Admin - كل الصلاحيات
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info('✅ Super Admin: ' . $superAdmin->permissions->count() . ' صلاحية');

        // مدير - كل شيء ماعدا manage_roles & manage_permissions
        $admin = Role::firstOrCreate(['name' => 'مدير', 'guard_name' => 'web']);
        $adminPerms = Permission::whereNotIn('name', ['manage_roles', 'manage_permissions'])->get();
        $admin->syncPermissions($adminPerms);
        $this->command->info('✅ مدير: ' . $admin->permissions->count() . ' صلاحية');

        // مودريتور - إدارة الشحنات والمنتجات
        $moderator = Role::firstOrCreate(['name' => 'مودريتور', 'guard_name' => 'web']);
        $moderator->syncPermissions([
            'view_dashboard',
            'view_shipments', 'create_shipments', 'edit_shipments', 'print_shipments', 'export_shipments',
            'view_products', 'create_products', 'edit_products',
            'view_shipping_companies', 'view_delivery_agents', 'assign_shipments',
        ]);
        $this->command->info('✅ مودريتور: ' . $moderator->permissions->count() . ' صلاحية');

        // محاسب - المالية والتقارير
        $accountant = Role::firstOrCreate(['name' => 'محاسب', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'view_dashboard', 'view_analytics', 'view_reports',
            'view_collections', 'create_collections', 'edit_collections', 'delete_collections',
            'view_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses',
            'view_shipments', 'view_products', // عرض فقط
        ]);
        $this->command->info('✅ محاسب: ' . $accountant->permissions->count() . ' صلاحية');

        // مندوب - عرض شحناته فقط
        $deliveryAgent = Role::firstOrCreate(['name' => 'مندوب', 'guard_name' => 'web']);
        $deliveryAgent->syncPermissions([
            'view_dashboard',
            'view_shipments', // سيتم تصفيتها حسب المندوب في Resource
        ]);
        $this->command->info('✅ مندوب: ' . $deliveryAgent->permissions->count() . ' صلاحية');

        // مشاهد - قراءة فقط لكل شيء
        $viewer = Role::firstOrCreate(['name' => 'مشاهد', 'guard_name' => 'web']);
        $viewerPerms = Permission::where('name', 'like', 'view_%')->get();
        $viewer->syncPermissions($viewerPerms);
        $this->command->info('✅ مشاهد: ' . $viewer->permissions->count() . ' صلاحية');

        // مندوب شركة شحن
        $shippingAgent = Role::firstOrCreate(['name' => 'مندوب شركة شحن', 'guard_name' => 'web']);
        $shippingAgent->syncPermissions([
            'view_dashboard',
            'view_shipments', // سيتم تصفيتها حسب شركة الشحن
        ]);
        $this->command->info('✅ مندوب شركة شحن: ' . $shippingAgent->permissions->count() . ' صلاحية');

        $this->command->info('🎉 تم إنشاء جميع الأدوار والصلاحيات بنجاح!');
    }
}
