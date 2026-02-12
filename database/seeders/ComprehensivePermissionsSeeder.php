<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ComprehensivePermissionsSeeder extends Seeder
{
    /**
     * نظام الصلاحيات الشامل - 338 صلاحية
     */
    public function run(): void
    {
        $this->command->info('🚀 بدء إنشاء نظام الصلاحيات الشامل...');

        // === 1. الشحنات (Shipments) - 35 صلاحية ===
        $this->createPermissionCategory('Shipments', [
            // CRUD
            'shipments.view_any', 'shipments.view', 'shipments.create', 
            'shipments.update', 'shipments.delete', 'shipments.restore', 'shipments.force_delete',
            
            // Bulk Operations
            'shipments.bulk_delete', 'shipments.bulk_update_status', 'shipments.bulk_assign_agent',
            
            // Export/Print
            'shipments.export_excel', 'shipments.export_pdf', 'shipments.print_invoices',
            'shipments.print_table', 'shipments.print_thermal', 
            
            // Import
            'shipments.import', 'shipments.download_template',
            
            // Status Management
            'shipments.update_status', 'shipments.mark_delivered', 'shipments.mark_returned',
            'shipments.mark_partial_return', 'shipments.reschedule',
            
            // Assignment
            'shipments.assign_agent', 'shipments.change_company', 
            'shipments.update_shipping_date', 'shipments.update_delivery_date', 'shipments.update_return_date',
            
            // Additional Info
            'shipments.add_notes', 'shipments.view_activity_log', 'shipments.view_print_history',
            'shipments.generate_barcode', 'shipments.update_tracking_number',
            
            // Filtering
            'shipments.filter_by_status', 'shipments.filter_by_company', 
            'shipments.filter_by_agent', 'shipments.advanced_search',
        ]);

        // === 2. المنتجات (Products) - 28 صلاحية ===
        $this->createPermissionCategory('Products', [
            // CRUD
            'products.view_any', 'products.view', 'products.create', 'products.update',
            'products.delete', 'products.restore', 'products.force_delete',
            
            // Prices
            'products.view_prices', 'products.update_price', 'products.manage_price_history',
            
            // Variants
            'products.view_variants', 'products.create_variant', 'products.update_variant',
            'products.delete_variant', 'products.manage_colors', 'products.manage_sizes',
            
            // Stock
            'products.view_stock', 'products.update_stock', 'products.view_stock_movements',
            'products.add_stock', 'products.remove_stock', 'products.set_stock_alert',
            'products.set_unlimited_stock',
            
            // Advanced
            'products.bulk_update', 'products.bulk_delete', 'products.export',
            'products.import', 'products.view_sales_stats',
        ]);

        // === 3. المستخدمين (Users) - 24 صلاحية ===
        $this->createPermissionCategory('Users', [
            // CRUD
            'users.view_any', 'users.view', 'users.create', 'users.update',
            'users.delete', 'users.restore', 'users.force_delete',
            
            // Account Management
            'users.activate', 'users.deactivate', 'users.reset_password',
            'users.change_email', 'users.update_profile',
            
            // Subscription
            'users.view_expiration', 'users.extend_subscription', 'users.set_lifetime',
            'users.view_expiring_users',
            
            // Roles & Permissions
            'users.assign_roles', 'users.assign_permissions', 'users.view_activity_log',
            'users.impersonate',
            
            // Bulk
            'users.bulk_activate', 'users.bulk_deactivate', 'users.bulk_extend', 'users.export',
        ]);

        // === 4. الأدوار والصلاحيات (Roles & Permissions) - 20 صلاحية ===
        $this->createPermissionCategory('Roles & Permissions', [
            // Roles CRUD
            'roles.view_any', 'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            
            // Role Permissions
            'roles.assign_permissions', 'roles.revoke_permissions', 'roles.view_permissions',
            'roles.sync_permissions',
            
            // Role Users
            'roles.assign_users', 'roles.revoke_users', 'roles.view_users',
            
            // Permissions CRUD
            'permissions.view_any', 'permissions.view', 'permissions.create',
            'permissions.update', 'permissions.delete',
            
            // Advanced
            'roles.clone', 'roles.export', 'permissions.sync_from_config',
        ]);

        // === 5. شركات الشحن (Shipping Companies) - 18 صلاحية ===
        $this->createPermissionCategory('Shipping Companies', [
            // CRUD
            'shipping_companies.view_any', 'shipping_companies.view', 'shipping_companies.create',
            'shipping_companies.update', 'shipping_companies.delete', 'shipping_companies.restore',
            'shipping_companies.force_delete',
            
            // Prices
            'shipping_companies.manage_prices', 'shipping_companies.set_default_price',
            'shipping_companies.set_governorate_prices',
            
            // Agents
            'shipping_companies.view_agents', 'shipping_companies.assign_agent',
            'shipping_companies.remove_agent',
            
            // Statistics
            'shipping_companies.view_statistics', 'shipping_companies.view_shipments',
            'shipping_companies.view_performance',
            
            // Advanced
            'shipping_companies.activate', 'shipping_companies.deactivate', 'shipping_companies.export',
        ]);

        // === 6. المندوبين (Delivery Agents) - 22 صلاحية ===
        $this->createPermissionCategory('Delivery Agents', [
            // CRUD
            'delivery_agents.view_any', 'delivery_agents.view', 'delivery_agents.create',
            'delivery_agents.update', 'delivery_agents.delete', 'delivery_agents.restore',
            'delivery_agents.force_delete',
            
            // Linking
            'delivery_agents.assign_user', 'delivery_agents.assign_company',
            'delivery_agents.remove_user', 'delivery_agents.remove_company',
            
            // Shipments
            'delivery_agents.view_shipments', 'delivery_agents.assign_shipments',
            'delivery_agents.view_pending_shipments', 'delivery_agents.view_delivered_shipments',
            
            // Statistics
            'delivery_agents.view_statistics', 'delivery_agents.view_performance',
            'delivery_agents.view_earnings', 'delivery_agents.view_collections',
            
            // Advanced
            'delivery_agents.activate', 'delivery_agents.deactivate', 'delivery_agents.export',
        ]);

        // === 7. التحصيلات (Collections) - 20 صلاحية ===
        $this->createPermissionCategory('Collections', [
            // CRUD
            'collections.view_any', 'collections.view', 'collections.create', 'collections.update',
            'collections.delete', 'collections.restore', 'collections.force_delete',
            
            // Status
            'collections.mark_confirmed', 'collections.mark_pending', 'collections.mark_cancelled',
            'collections.approve',
            
            // Data
            'collections.add_payment_proof', 'collections.view_payment_proof',
            'collections.update_amount', 'collections.add_notes',
            
            // Reports
            'collections.view_statistics', 'collections.export_excel', 'collections.export_pdf',
            'collections.print',
            
            // Bulk
            'collections.bulk_approve', 'collections.bulk_delete',
        ]);

        // === 8. المصروفات (Expenses) - 20 صلاحية ===
        $this->createPermissionCategory('Expenses', [
            // CRUD
            'expenses.view_any', 'expenses.view', 'expenses.create', 'expenses.update',
            'expenses.delete', 'expenses.restore', 'expenses.force_delete',
            
            // Categories
            'expenses.manage_categories', 'expenses.assign_category', 'expenses.view_by_category',
            
            // Status
            'expenses.mark_approved', 'expenses.mark_pending', 'expenses.mark_rejected',
            'expenses.approve',
            
            // Attachments
            'expenses.add_receipt', 'expenses.view_receipt', 'expenses.delete_receipt',
            
            // Reports
            'expenses.export_excel', 'expenses.export_pdf', 'expenses.view_statistics',
        ]);

        // === 9. حالات الشحن (Shipment Statuses) - 15 صلاحية ===
        $this->createPermissionCategory('Shipment Statuses', [
            // CRUD
            'statuses.view_any', 'statuses.view', 'statuses.create', 'statuses.update', 'statuses.delete',
            
            // Management
            'statuses.set_color', 'statuses.set_icon', 'statuses.set_code', 'statuses.set_default',
            
            // Ordering
            'statuses.reorder', 'statuses.set_priority',
            
            // Advanced
            'statuses.activate', 'statuses.deactivate', 'statuses.view_shipment_count', 'statuses.export',
        ]);

        // === 10. المخزون (Inventory & Stock Movements) - 25 صلاحية ===
        $this->createPermissionCategory('Inventory', [
            // CRUD
            'inventory.view_any', 'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
            
            // Operations
            'inventory.add_stock', 'inventory.remove_stock', 'inventory.set_quantity',
            'inventory.set_unlimited', 'inventory.set_alert_level', 'inventory.update_alert',
            
            // Stock Movements
            'stock_movements.view_any', 'stock_movements.view', 'stock_movements.create',
            'stock_movements.delete',
            
            // Reports
            'inventory.view_low_stock', 'inventory.view_out_of_stock', 'inventory.view_overstocked',
            'inventory.view_movements_report',
            
            // Advanced
            'inventory.bulk_update', 'inventory.export_excel', 'inventory.import',
            'inventory.run_stocktaking', 'inventory.adjust_stock', 'inventory.reserve_stock',
        ]);

        // === 11. التقارير (Reports) - 30 صلاحية ===
        $this->createPermissionCategory('Reports', [
            // Shipments Reports
            'reports.shipments.view', 'reports.shipments.filter', 'reports.shipments.export_excel',
            'reports.shipments.export_pdf', 'reports.shipments.print', 'reports.shipments.by_status',
            'reports.shipments.by_company', 'reports.shipments.by_agent', 'reports.shipments.by_product',
            
            // Accounting Reports
            'reports.collections.view', 'reports.collections.export', 'reports.expenses.view',
            'reports.expenses.export', 'reports.cashbox.view', 'reports.cashbox.export',
            'reports.treasury.view', 'reports.treasury.export',
            
            // Time-based Reports
            'reports.daily.view', 'reports.monthly.view', 'reports.yearly.view',
            
            // Performance Reports
            'reports.performance.companies', 'reports.performance.agents', 'reports.performance.products',
            
            // Custom Reports
            'reports.custom.create', 'reports.custom.save', 'reports.custom.delete', 'reports.custom.share',
            
            // Advanced
            'reports.schedule.create', 'reports.email.send', 'reports.analytics.view',
        ]);

        // === 12. الإعدادات (Settings) - 25 صلاحية ===
        $this->createPermissionCategory('Settings', [
            // General
            'settings.view', 'settings.update', 'settings.general', 'settings.company_info',
            
            // System
            'settings.system.view', 'settings.system.update', 'settings.date_format',
            'settings.time_format', 'settings.timezone', 'settings.language',
            
            // Email
            'settings.email.view', 'settings.email.update', 'settings.email.test',
            
            // Notifications
            'settings.notifications.view', 'settings.notifications.update', 'settings.notifications.email',
            'settings.notifications.sms',
            
            // Backup
            'settings.backup.view', 'settings.backup.create', 'settings.backup.download',
            'settings.backup.restore', 'settings.backup.delete',
            
            // Google Sheets
            'settings.google_sheets.view', 'settings.google_sheets.update', 'settings.google_sheets.sync',
        ]);

        // === 13. لوحة التحكم (Dashboard) - 15 صلاحية ===
        $this->createPermissionCategory('Dashboard', [
            // Basic
            'dashboard.view', 'dashboard.view_statistics', 'dashboard.view_charts',
            
            // Widgets
            'dashboard.widget.shipments_overview', 'dashboard.widget.revenue_chart',
            'dashboard.widget.financial_stats', 'dashboard.widget.recent_shipments',
            'dashboard.widget.delayed_shipments', 'dashboard.widget.company_performance',
            'dashboard.widget.low_stock_alerts',
            
            // Customization
            'dashboard.customize_layout', 'dashboard.add_widget', 'dashboard.remove_widget',
            'dashboard.reorder_widgets', 'dashboard.export_data',
        ]);

        // === 14. سجل النشاط (Activity Log) - 10 صلاحيات ===
        $this->createPermissionCategory('Activity Log', [
            'activity_log.view_any', 'activity_log.view', 'activity_log.filter_by_user',
            'activity_log.filter_by_model', 'activity_log.filter_by_date', 'activity_log.export',
            'activity_log.delete_old', 'activity_log.view_user_activity',
            'activity_log.view_model_activity', 'activity_log.clear_all',
        ]);

        // === 15. البحث المتقدم (Advanced Search) - 8 صلاحيات ===
        $this->createPermissionCategory('Advanced Search', [
            'search.global', 'search.shipments', 'search.products', 'search.users',
            'search.customers', 'search.saved_searches', 'search.export_results',
            'search.advanced_filters',
        ]);

        // === 16. الإشعارات (Notifications) - 12 صلاحية ===
        $this->createPermissionCategory('Notifications', [
            'notifications.view', 'notifications.mark_read', 'notifications.mark_unread',
            'notifications.delete', 'notifications.delete_all', 'notifications.preferences',
            'notifications.email_toggle', 'notifications.sms_toggle', 'notifications.in_app_toggle',
            'notifications.test_send', 'notifications.broadcast', 'notifications.schedule',
        ]);

        // === 17. إدارة الملفات (Files & Media) - 10 صلاحيات ===
        $this->createPermissionCategory('Files & Media', [
            'media.view', 'media.upload', 'media.download', 'media.delete',
            'media.view_storage_usage', 'media.manage_folders', 'media.share',
            'media.compress', 'media.bulk_delete', 'media.clear_cache',
        ]);

        $this->command->info('✅ تم إنشاء جميع الصلاحيات بنجاح!');
        $this->command->info('📊 إجمالي الصلاحيات: ' . Permission::count());
    }

    /**
     * إنشاء مجموعة صلاحيات حسب الفئة
     */
    private function createPermissionCategory(string $category, array $permissions): void
    {
        $count = 0;
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
            $count++;
        }
        
        $this->command->info("✓ {$category}: {$count} صلاحية");
    }
}
