<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateStandardRoles extends Command
{
    protected $signature = 'roles:create-standard';
    protected $description = 'Create standard roles (Super Admin, Manager, Moderator, etc.) and assign permissions';

    public function handle()
    {
        $this->info('🚀 بدء إنشاء الأدوار الأساسية...');

        // Fix Postgres Sequence for Roles
        try {
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
                $maxId = \Illuminate\Support\Facades\DB::table('roles')->max('id') ?? 0;
                \Illuminate\Support\Facades\DB::statement("SELECT setval('roles_id_seq', " . ($maxId + 1) . ", false)");
                $this->info('🔧 تم إصلاح تسلسل IDs للأدوار في قاعدة البيانات.');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️ لم نتمكن من إصلاح التسلسل: ' . $e->getMessage());
        }

        // 1. Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->info('✅ تم إنشاء/التحقق من دور: Super Admin (كامل الصلاحيات)');

        // 2. Manager (Manager)
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerPermissions = Permission::where('name', 'LIKE', 'shipments.%')
            ->orWhere('name', 'LIKE', 'products.%')
            ->orWhere('name', 'LIKE', 'reports.%')
            ->orWhere('name', 'LIKE', 'users.view%')
            ->orWhere('name', 'LIKE', 'dashboard.%')
            ->get();
        $manager->syncPermissions($managerPermissions);
        $this->info('✅ تم إعداد دور: Manager (' . $managerPermissions->count() . ' صلاحية)');

        // 3. Accountant (Accountant)
        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountantPermissions = Permission::where('name', 'LIKE', 'collections.%')
            ->orWhere('name', 'LIKE', 'expenses.%')
            ->orWhere('name', 'LIKE', 'reports.collections%')
            ->orWhere('name', 'LIKE', 'reports.expenses%')
            ->orWhere('name', 'LIKE', 'reports.treasury%')
            ->orWhere('name', 'LIKE', 'dashboard.view')
            ->orWhere('name', 'LIKE', 'dashboard.widget.financial%')
            ->get();
        $accountant->syncPermissions($accountantPermissions);
        $this->info('✅ تم إعداد دور: Accountant (' . $accountantPermissions->count() . ' صلاحية)');

        // 4. Warehouse Manager (Stock Manager)
        $stockManager = Role::firstOrCreate(['name' => 'Stock Manager', 'guard_name' => 'web']);
        $stockPermissions = Permission::where('name', 'LIKE', 'inventory.%')
            ->orWhere('name', 'LIKE', 'stock_movements.%')
            ->orWhere('name', 'LIKE', 'products.view%')
            ->orWhere('name', 'LIKE', 'products.update_stock')
            ->orWhere('name', 'LIKE', 'dashboard.widget.low_stock%')
            ->get();
        $stockManager->syncPermissions($stockPermissions);
        $this->info('✅ تم إعداد دور: Stock Manager (' . $stockPermissions->count() . ' صلاحية)');

        // 5. Operations Agent (Shipping Agent) - renamed to readable
        $agent = Role::firstOrCreate(['name' => 'Operations Agent', 'guard_name' => 'web']);
        $agentPermissions = Permission::where('name', 'shipments.view_any')
            ->orWhere('name', 'shipments.view')
            ->orWhere('name', 'shipments.create')
            ->orWhere('name', 'shipments.update_status')
            ->orWhere('name', 'shipments.print%')
            ->get();
        $agent->syncPermissions($agentPermissions);
        $this->info('✅ تم إعداد دور: Operations Agent (' . $agentPermissions->count() . ' صلاحية)');

        // 6. Viewer (Read Only)
        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewerPermissions = Permission::where('name', 'LIKE', '%.view%')
            ->where('name', 'NOT LIKE', 'settings.%')
            ->where('name', 'NOT LIKE', 'roles.%')
            ->where('name', 'NOT LIKE', 'users.%')
            ->get();
        $viewer->syncPermissions($viewerPermissions);
        $this->info('✅ تم إعداد دور: Viewer (' . $viewerPermissions->count() . ' صلاحية)');

        $this->newLine();
        $this->info('🎉 تم الانتهاء! يمكنك الآن تعيين هذه الأدوار للمستخدمين.');
        
        return Command::SUCCESS;
    }
}
