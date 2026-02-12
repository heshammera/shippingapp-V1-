<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermissions extends Command
{
    protected $signature = 'permissions:create';
    protected $description = 'Create all comprehensive permissions (338 total)';

    public function handle()
    {
        $this->info('🚀 بدء إنشاء نظام الصلاحيات الشامل...');
        
        // Fix Postgres Sequence
        try {
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
                $maxId = \Illuminate\Support\Facades\DB::table('permissions')->max('id') ?? 0;
                \Illuminate\Support\Facades\DB::statement("SELECT setval('permissions_id_seq', " . ($maxId + 1) . ", false)");
                $this->info('🔧 تم إصلاح تسلسل IDs في قاعدة البيانات.');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️ لم نتمكن من إصلاح التسلسل: ' . $e->getMessage());
        }

        $allPermissions = $this->getAllPermissions();
        
        $bar = $this->output->createProgressBar(count($allPermissions));
        $bar->start();
        
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info('✅ تم إنشاء جميع الصلاحيات بنجاح!');
        $this->info('📊 إجمالي الصلاحيات: ' . Permission::count());
        
        return Command::SUCCESS;
    }

    private function getAllPermissions(): array
    {
        return [
            // 1. Shipments (35)
            'shipments.view_any', 'shipments.view', 'shipments.create', 'shipments.update', 'shipments.delete', 
            'shipments.restore', 'shipments.force_delete', 'shipments.bulk_delete', 'shipments.bulk_update_status', 
            'shipments.bulk_assign_agent', 'shipments.export_excel', 'shipments.export_pdf', 'shipments.print_invoices',
            'shipments.print_table', 'shipments.print_thermal', 'shipments.import', 'shipments.download_template',
            'shipments.update_status', 'shipments.mark_delivered', 'shipments.mark_returned', 'shipments.mark_partial_return',
            'shipments.reschedule', 'shipments.assign_agent', 'shipments.change_company', 'shipments.update_shipping_date',
            'shipments.update_delivery_date', 'shipments.update_return_date', 'shipments.add_notes', 
            'shipments.view_activity_log', 'shipments.view_print_history', 'shipments.generate_barcode',
            'shipments.update_tracking_number', 'shipments.filter_by_status', 'shipments.filter_by_company',
            'shipments.filter_by_agent', 'shipments.advanced_search',
            
            // 2. Products (28)
            'products.view_any', 'products.view', 'products.create', 'products.update', 'products.delete',
            'products.restore', 'products.force_delete', 'products.view_prices', 'products.update_price',
            'products.manage_price_history', 'products.view_variants', 'products.create_variant', 'products.update_variant',
            'products.delete_variant', 'products.manage_colors', 'products.manage_sizes', 'products.view_stock',
            'products.update_stock', 'products.view_stock_movements', 'products.add_stock', 'products.remove_stock',
            'products.set_stock_alert', 'products.set_unlimited_stock', 'products.bulk_update', 'products.bulk_delete',
            'products.export', 'products.import', 'products.view_sales_stats',
            
            // 3. Users (24)
            'users.view_any', 'users.view', 'users.create', 'users.update', 'users.delete', 'users.restore',
            'users.force_delete', 'users.activate', 'users.deactivate', 'users.reset_password', 'users.change_email',
            'users.update_profile', 'users.view_expiration', 'users.extend_subscription', 'users.set_lifetime',
            'users.view_expiring_users', 'users.assign_roles', 'users.assign_permissions', 'users.view_activity_log',
            'users.impersonate', 'users.bulk_activate', 'users.bulk_deactivate', 'users.bulk_extend', 'users.export',
            
            // 4. Roles & Permissions (20)
            'roles.view_any', 'roles.view', 'roles.create', 'roles.update', 'roles.delete', 'roles.assign_permissions',
            'roles.revoke_permissions', 'roles.view_permissions', 'roles.sync_permissions', 'roles.assign_users',
            'roles.revoke_users', 'roles.view_users', 'permissions.view_any', 'permissions.view', 'permissions.create',
            'permissions.update', 'permissions.delete', 'roles.clone', 'roles.export', 'permissions.sync_from_config',
            
            // 5. Shipping Companies (18)
            'shipping_companies.view_any', 'shipping_companies.view', 'shipping_companies.create', 'shipping_companies.update',
            'shipping_companies.delete', 'shipping_companies.restore', 'shipping_companies.force_delete',
            'shipping_companies.manage_prices', 'shipping_companies.set_default_price', 'shipping_companies.set_governorate_prices',
            'shipping_companies.view_agents', 'shipping_companies.assign_agent', 'shipping_companies.remove_agent',
            'shipping_companies.view_statistics', 'shipping_companies.view_shipments', 'shipping_companies.view_performance',
            'shipping_companies.activate', 'shipping_companies.deactivate', 'shipping_companies.export',
            
            // 6. Delivery Agents (22)
            'delivery_agents.view_any', 'delivery_agents.view', 'delivery_agents.create', 'delivery_agents.update',
            'delivery_agents.delete', 'delivery_agents.restore', 'delivery_agents.force_delete', 'delivery_agents.assign_user',
            'delivery_agents.assign_company', 'delivery_agents.remove_user', 'delivery_agents.remove_company',
            'delivery_agents.view_shipments', 'delivery_agents.assign_shipments', 'delivery_agents.view_pending_shipments',
            'delivery_agents.view_delivered_shipments', 'delivery_agents.view_statistics', 'delivery_agents.view_performance',
            'delivery_agents.view_earnings', 'delivery_agents.view_collections', 'delivery_agents.activate',
            'delivery_agents.deactivate', 'delivery_agents.export',
            
            // 7. Collections (20)
            'collections.view_any', 'collections.view', 'collections.create', 'collections.update', 'collections.delete',
            'collections.restore', 'collections.force_delete', 'collections.mark_confirmed', 'collections.mark_pending',
            'collections.mark_cancelled', 'collections.approve', 'collections.add_payment_proof', 'collections.view_payment_proof',
            'collections.update_amount', 'collections.add_notes', 'collections.view_statistics', 'collections.export_excel',
            'collections.export_pdf', 'collections.print', 'collections.bulk_approve', 'collections.bulk_delete',
            
            // 8. Expenses (20)
            'expenses.view_any', 'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete', 'expenses.restore',
            'expenses.force_delete', 'expenses.manage_categories', 'expenses.assign_category', 'expenses.view_by_category',
            'expenses.mark_approved', 'expenses.mark_pending', 'expenses.mark_rejected', 'expenses.approve',
            'expenses.add_receipt', 'expenses.view_receipt', 'expenses.delete_receipt', 'expenses.export_excel',
            'expenses.export_pdf', 'expenses.view_statistics',
            
            // 9. Shipment Statuses (15)
            'statuses.view_any', 'statuses.view', 'statuses.create', 'statuses.update', 'statuses.delete',
            'statuses.set_color', 'statuses.set_icon', 'statuses.set_code', 'statuses.set_default', 'statuses.reorder',
            'statuses.set_priority', 'statuses.activate', 'statuses.deactivate', 'statuses.view_shipment_count',
            'statuses.export',
            
            // 10. Inventory (25)
            'inventory.view_any', 'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
            'inventory.add_stock', 'inventory.remove_stock', 'inventory.set_quantity', 'inventory.set_unlimited',
            'inventory.set_alert_level', 'inventory.update_alert', 'stock_movements.view_any', 'stock_movements.view',
            'stock_movements.create', 'stock_movements.delete', 'inventory.view_low_stock', 'inventory.view_out_of_stock',
            'inventory.view_overstocked', 'inventory.view_movements_report', 'inventory.bulk_update', 'inventory.export_excel',
            'inventory.import', 'inventory.run_stocktaking', 'inventory.adjust_stock', 'inventory.reserve_stock',
            
            // 11. Reports (30)
            'reports.shipments.view', 'reports.shipments.filter', 'reports.shipments.export_excel', 'reports.shipments.export_pdf',
            'reports.shipments.print', 'reports.shipments.by_status', 'reports.shipments.by_company', 'reports.shipments.by_agent',
            'reports.shipments.by_product', 'reports.collections.view', 'reports.collections.export', 'reports.expenses.view',
            'reports.expenses.export', 'reports.cashbox.view', 'reports.cashbox.export', 'reports.treasury.view',
            'reports.treasury.export', 'reports.daily.view', 'reports.monthly.view', 'reports.yearly.view',
            'reports.performance.companies', 'reports.performance.agents', 'reports.performance.products',
            'reports.custom.create', 'reports.custom.save', 'reports.custom.delete', 'reports.custom.share',
            'reports.schedule.create', 'reports.email.send', 'reports.analytics.view',
            
            // 12. Settings (25)
            'settings.view', 'settings.update', 'settings.general', 'settings.company_info', 'settings.system.view',
            'settings.system.update', 'settings.date_format', 'settings.time_format', 'settings.timezone', 'settings.language',
            'settings.email.view', 'settings.email.update', 'settings.email.test', 'settings.notifications.view',
            'settings.notifications.update', 'settings.notifications.email', 'settings.notifications.sms',
            'settings.backup.view', 'settings.backup.create', 'settings.backup.download', 'settings.backup.restore',
            'settings.backup.delete', 'settings.google_sheets.view', 'settings.google_sheets.update', 'settings.google_sheets.sync',
            
            // 13. Dashboard (15)
            'dashboard.view', 'dashboard.view_statistics', 'dashboard.view_charts', 'dashboard.widget.shipments_overview',
            'dashboard.widget.revenue_chart', 'dashboard.widget.financial_stats', 'dashboard.widget.recent_shipments',
            'dashboard.widget.delayed_shipments', 'dashboard.widget.company_performance', 'dashboard.widget.low_stock_alerts',
            'dashboard.customize_layout', 'dashboard.add_widget', 'dashboard.remove_widget', 'dashboard.reorder_widgets',
            'dashboard.export_data',
            
            // 14. Activity Log (10)
            'activity_log.view_any', 'activity_log.view', 'activity_log.filter_by_user', 'activity_log.filter_by_model',
            'activity_log.filter_by_date', 'activity_log.export', 'activity_log.delete_old', 'activity_log.view_user_activity',
            'activity_log.view_model_activity', 'activity_log.clear_all',
            
            // 15. Search (8)
            'search.global', 'search.shipments', 'search.products', 'search.users', 'search.customers',
            'search.saved_searches', 'search.export_results', 'search.advanced_filters',
            
            // 16. Notifications (12)
            'notifications.view', 'notifications.mark_read', 'notifications.mark_unread', 'notifications.delete',
            'notifications.delete_all', 'notifications.preferences', 'notifications.email_toggle', 'notifications.sms_toggle',
            'notifications.in_app_toggle', 'notifications.test_send', 'notifications.broadcast', 'notifications.schedule',
            
            // 17. Media (10)
            'media.view', 'media.upload', 'media.download', 'media.delete', 'media.view_storage_usage',
            'media.manage_folders', 'media.share', 'media.compress', 'media.bulk_delete', 'media.clear_cache',
        ];
    }
}
