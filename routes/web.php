<?php
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\Reports\CashboxReportController;
use App\Http\Controllers\ShipmentReportController;

use App\Http\Controllers\Reports\ExpensesReportController;
use App\Http\Controllers\Reports\CollectionsReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Services\GoogleSheetImporter;
use App\Http\Controllers\{
    DashboardController,
    ShipmentController,
    ShipmentExportController,
    ShippingCompanyController,
    CollectionController,
    DeliveryAgentController,
    ExpenseController,
    AccountingController,
    ReportController,
    ProductController,
    ShipmentStatusController,
    ShipmentActionController,
    ShipmentImportController,
    SettingController,
    UserController,
    RoleController,
    ExpenseReportController,
    InventoryController
};

// Route::redirect('/', '/admin');





//Route::get('expenses', [Reports\ExpensesReportController::class, 'index'])
//     ->name('expenses.index');
//Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
//Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
//Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
//Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');




Route::patch('/shipments/{shipment}/status-company', [\App\Http\Controllers\ShipmentController::class, 'updateStatusAndCompany'])
    ->name('shipments.updateStatusCompany');





Route::prefix('inventories')->name('inventories.')->group(function () {
    Route::put('{inventory}/alert', [InventoryController::class, 'updateAlert'])->name('alert');
    Route::post('{inventory}/add', [InventoryController::class, 'add'])->name('add');
    Route::post('{inventory}/remove', [InventoryController::class, 'remove'])->name('remove');
    Route::put('{inventory}/unlimited', [InventoryController::class, 'setUnlimited'])->name('setUnlimited');
    Route::put('{inventory}/quantity', [InventoryController::class, 'setQuantity'])->name('setQuantity');
});

Route::get('/products/{product}/options', [\App\Http\Controllers\ProductController::class, 'options'])
    ->name('products.options');


Route::delete('/inventories/{inventory}', [InventoryController::class, 'destroy'])->name('inventories.destroy');         // حذف (Soft)
Route::post('/inventories/{id}/restore', [InventoryController::class, 'restore'])->name('inventories.restore');          // استعادة
Route::delete('/inventories/{id}/force', [InventoryController::class, 'forceDelete'])->name('inventories.forceDelete');  // حذف نهائي


Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');


Route::resource('expenses', ExpenseController::class);
Route::get('/products/{id}/details', [App\Http\Controllers\ProductController::class, 'getDetails']);
Route::match(['put','post'], 'inventories/{inventory}/alert', [InventoryController::class, 'updateAlert'])
    ->name('inventories.updateAlert');
Route::post('inventories/{inventory}/unlimit', [InventoryController::class, 'unlimit'])
    ->name('inventories.unlimit');
//Route::post('inventories/{inventory}/set-quantity', [InventoryController::class, 'setQuantity'])
//    ->name('inventories.setQuantity');
Route::put('inventories/{inventory}/toggle-unlimited', [\App\Http\Controllers\InventoryController::class, 'toggleUnlimited'])->name('inventories.toggleUnlimited');


//Route::resource('expenses', ExpenseController::class);
Route::resource('product-prices', ProductPriceController::class);
Route::get('/reports/collections/pdf', [CollectionsReportController::class, 'exportPdf'])->name('reports.collections.pdf');
Route::get('/reports/collections/print', [CollectionsReportController::class, 'printPdf'])->name('reports.collections.print');
Route::get('/reports/collections/excel', [CollectionsReportController::class, 'exportExcel'])->name('reports.collections.excel');
// تقرير الخزنة
Route::get('reports/cashbox', [CashboxReportController::class, 'index'])->name('reports.cashbox');
Route::get('reports/cashbox/pdf', [CashboxReportController::class, 'exportPdf'])->name('reports.cashbox.pdf');
Route::get('reports/cashbox/print', [CashboxReportController::class, 'printPdf'])->name('reports.cashbox.print');
Route::get('reports/cashbox/excel', [CashboxReportController::class, 'exportExcel'])->name('reports.cashbox.excel');
// صفحة عرض التقرير مع الفلاتر (GET)
Route::get('/reports/shipments', [ShipmentReportController::class, 'index'])->name('reports.shipments');

// تصدير Excel
Route::get('/reports/shipments/excel', [ShipmentReportController::class, 'exportExcel'])->name('reports.shipments.excel');

// تصدير PDF
Route::get('/reports/shipments/pdf', [ShipmentReportController::class, 'exportPdf'])->name('reports.shipments.pdf');

// طباعة التقرير (صفحة منفصلة أو تستخدم نفس صفحة التقرير مع تصميم طباعة)
Route::get('/reports/shipments/print', [ShipmentReportController::class, 'print'])->name('reports.shipments.print');


Route::get('/reports/expenses', [ExpensesReportController::class, 'index'])->name('reports.expenses');
Route::get('/reports/expenses/pdf', [ExpensesReportController::class, 'exportPdf'])->name('reports.expenses.pdf');
Route::get('/reports/expenses/print', [ExpensesReportController::class, 'printPdf'])->name('reports.expenses.print');
Route::get('/reports/expenses/excel', [ExpensesReportController::class, 'exportExcel'])->name('reports.expenses.excel');
Route::get('main-expenses', [ExpenseController::class, 'index'])->name('expenses.index');

Route::get('/reports/expenses', [ExpenseReportController::class, 'expenses'])->name('reports.expenses');

Route::delete('/shipments/{id}/quick-delete', [ShipmentController::class, 'quickDelete']);

Route::prefix('backup')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
    Route::post('/create', [\App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
    Route::post('/create-db', [\App\Http\Controllers\BackupController::class, 'createDb'])->name('backup.create.db');
    Route::get('/list', [\App\Http\Controllers\BackupController::class, 'listBackups'])->name('backup.list');
    Route::get('/download/{file}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
    Route::get('/delete/{file}', [\App\Http\Controllers\BackupController::class, 'delete'])->name('backup.delete');
    Route::get('/restore/{file}', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backup.restore');
});
Route::middleware('auth')->post('/user/theme-color', [UserController::class, 'updateThemeColor']);

Route::get('/products/{product}/prices', [ProductPriceController::class, 'edit'])->name('product.prices.edit');
Route::post('/products/{product}/prices', [ProductPriceController::class, 'update'])->name('product.prices.update');
Route::get('/admin/sync-google-sheet', function () {
    app(GoogleSheetImporter::class)->importOrders();
    return back()->with('success', '✅ تمت مزامنة الشحنات من Google Sheet بنجاح!');
})->middleware('auth');

// 🔥 TEMPORARY MIGRATION ROUTE
Route::get('/migrate-db', function() {
    try {
        // Force run the specific migration file for sessions
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true,
            '--path' => 'database/migrations/2025_01_01_000000_create_sessions_table.php'
        ]);
        
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        // Also try running all migrations to be sure
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output .= "\n" . \Illuminate\Support\Facades\Artisan::output();

        return '<h1>✅ Migration Completed Successfully</h1><pre>' . $output . '</pre>';
    } catch (\Exception $e) {
        return '<h1>❌ Migration Failed</h1><pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
    }
});

// ✅ صفحة الدخول
Route::middleware('guest')->group(function () {
    // Fallback for Filament POST login attempts (fixes MethodNotAllowed)
    Route::post('/admin/login', function () {
        return redirect()->route('filament.admin.auth.login');
    });

    Route::view('/login', 'auth.login')->name('login');

    Route::post('/login', function (Request $request) {
        if (Auth::attempt(['name' => $request->name, 'password' => $request->password])) {
            return redirect()->intended('/redirect-by-role');
        }
        return back()->with('error', 'بيانات الدخول غير صحيحة');
    })->name('login.post');
});


// ✅ تسجيل الخروج
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ✅ التوجيه حسب الدور
Route::get('/redirect-by-role', function () {
    $user = auth()->user();
    
    // Check Spatie Roles first if available, otherwise check column 'role'
    if ($user->hasRole('Super Admin')) {
        return redirect()->route('dashboard');
    }

    return match ($user->role) {
        'admin' => redirect()->route('dashboard'),
        'accountant' => redirect()->route('accounting.index'),
        'moderator' => redirect()->route('shipments.create'),
        'delivery_agent' => redirect()->route('shipments.index'),
        'viewer' => redirect()->route('dashboard'),
        'shipping_agent' => redirect()->route('shipments.index'),
        default => redirect()->route('dashboard'), // Fallback to dashboard instead of login to prevent loops
    };
})->middleware('auth')->name('redirect.by.role');


// ✅ الصفحة الرئيسية توجّه حسب الدور إذا كان المستخدم مسجل
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('redirect.by.role')
        : redirect()->route('login');
});

// ✅ لوحة التحكم للأدمن فقط
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// ✅ صفحة الشحنات (لكل المسجلين)
Route::get('/shipments', [ShipmentController::class, 'index'])
    ->middleware(['auth'])
    ->name('shipments.index');

Route::get('/test-email', function () {
    try {
        Mail::raw('هذه رسالة تجريبية من إعدادات SMTP.', function ($message) {
            $message->to('hesham.mera@gmail.com') // ← غيّرها لبريدك
                    ->subject('اختبار البريد من Laravel');
        });

        return 'تم إرسال البريد بنجاح.';
    } catch (\Exception $e) {
        return 'فشل الإرسال: ' . $e->getMessage();
    }
})->middleware('auth');

    Route::get('/products/{product}/details', function(App\Models\Product $product) {
        return response()->json([
            'price' => $product->price,
            'colors' => explode(',', $product->colors),
            'sizes' => explode(',', $product->sizes)
        ]);
    });
// ---------------------------
// للمستخدم المسجل
// ---------------------------
Route::middleware(['web', 'auth', 'shipping_agent.access', 'prevent.viewer.modification', 'check.expiration', 'restrict.by.role', 'role.access'])->group(function () {
    //Route::delete('/shipments/bulk-delete', [\App\Http\Controllers\ShipmentController::class, 'bulkDelete'])->name('shipments.bulk-delete');
    Route::get('/delivery-agents/{agent}/shipments', [\App\Http\Controllers\DeliveryAgentController::class, 'shipments'])->name('delivery-agents.shipments');
Route::post('/shipments/{shipment}/quick-update', [ShipmentController::class, 'quickUpdate'])->name('shipments.quick-update');
Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
Route::resource('collections', CollectionController::class);
Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');

Route::delete('/shipments/{shipment}/quick-delete', [ShipmentController::class, 'quickDelete'])->name('shipments.quick-delete');

// inventories
    Route::get('inventories', [InventoryController::class, 'index'])->name('inventories.index');
    Route::get('inventories/create', [InventoryController::class, 'create'])->name('inventories.create');
    Route::post('inventories', [InventoryController::class, 'store'])->name('inventories.store');
    Route::post('inventories/{inventory}/add', [InventoryController::class, 'add'])->name('inventories.add');
    Route::post('inventories/{inventory}/remove', [InventoryController::class, 'remove'])->name('inventories.remove');
    
    
    
    
Route::get('/settings/google-sheet', [SettingController::class, 'googleSheet'])->name('settings.google_sheet');
Route::post('/settings/google-sheet', [SettingController::class, 'updateGoogleSheet'])->name('settings.google_sheet.update');



    Route::delete('/shipments/bulk-delete', [ShipmentController::class, 'bulkDelete'])->name('shipments.bulk-delete');
Route::match(['post', 'put', 'patch'], 'shipments/{shipment}/update-status', [ShipmentController::class, 'updateStatus'])
    ->name('shipments.updateStatus');

Route::post('/shipments/{shipment}/assign-agent', [ShipmentController::class, 'assignAgent']);
//Route::post('/shipments/{shipment}/assign-agent', [\App\Http\Controllers\ShipmentController::class, 'assignAgent']);
Route::post('/shipments/{shipment}/assign-agent', [ShipmentController::class, 'assignAgent'])->name('shipments.assignAgent');

    Route::post('/shipments/import', [ShipmentController::class, 'import'])->name('shipments.import');
    Route::get('/shipments/import', [ShipmentController::class, 'importForm'])->name('shipments.import.form');

    Route::get('/settings/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
    Route::get('/settings/system', [SettingController::class, 'system'])->name('settings.system');
    Route::post('/settings/create-backup', [SettingController::class, 'createBackup'])->name('settings.create-backup');

    Route::post('/settings/update-notifications', [SettingController::class, 'updateNotifications'])->name('settings.update-notifications');
    Route::post('/settings/update-system', [SettingController::class, 'updateSystem'])->name('settings.update-system');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

    Route::post('/settings/update-system', [SettingController::class, 'updateSystem'])
    ->name('settings.update-system');
    Route::post('/settings/update-notifications', [SettingController::class, 'updateNotifications'])
    ->name('settings.update-notifications');

    Route::post('/shipments/{shipment}/update-return-date', [ShipmentController::class, 'updateReturnDate'])
    ->name('shipments.update-return-date');

Route::post('/shipments/update-shipping-company/{shipment}', [ShipmentController::class, 'updateShippingCompany'])->name('shipments.updateShippingCompany');



//Route::match(['put', 'post'], '/shipments/{shipment}/update-status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
Route::put('shipments/{shipment}/update-delivery', [ShipmentController::class, 'updateDeliveryDetails'])->name('shipments.updateDeliveryDetails');

    Route::get('expenses', [ExpensesReportController::class, 'index'])->name('expenses.report');
    Route::get('/reports/collections', [CollectionsReportController::class, 'index'])->name('collections.report');
    Route::get('/collections/{collection}/edit', [CollectionController::class, 'edit'])->name('collections.edit');
Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
        Route::get('/shipments/export-print', [\App\Http\Controllers\ShipmentExportController::class, 'exportPrint'])->name('shipments.export.print');
//Route::post('shipments/{shipment}/quick-update', [ShipmentController::class, 'quickUpdate'])->name('shipments.quick-update');
            Route::get('shipments/excel', [ReportController::class, 'exportShipmentsExcel'])->name('reports.shipments.excel');
    Route::get('/shipments/excel', [ReportController::class, 'exportShipmentsExcel'])->name('shipments.excel');
//Route::post('/shipments/update-shipping-company/{shipment}', [ShipmentController::class, 'updateShippingCompany'])->name('shipments.update-company');
            Route::get('/shipments/print-selected', [ShipmentController::class, 'printSelected'])->name('shipments.print.selected');
            
        Route::get('/shipments/print-invoices', [ShipmentActionController::class, 'printInvoices'])->name('shipments.print.invoices');
        Route::get('/shipments/print-table', [ShipmentActionController::class, 'printTable'])->name('shipments.print.table');
        Route::get('/shipments/print-thermal', [ShipmentActionController::class, 'printThermal'])->name('shipments.print.thermal');
        Route::get('/shipments/export-excel', [ShipmentActionController::class, 'export'])->name('shipments.export');
        
        Route::get('import/form', [ShipmentImportController::class, 'form'])->name('shipments.import.form');
        Route::get('import/template', [ShipmentImportController::class, 'downloadTemplate'])->name('shipments.import.template');
        Route::post('import/process', [ShipmentImportController::class, 'import'])->name('shipments.import');
        
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create');
    Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
//Route::post('/shipments/{shipment}/update-status', [ShipmentController::class, 'updateStatus'])
//    ->name('shipments.updateStatus');

    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    //Route::put('{shipment}/update-status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
    //Route::resource('expenses', ExpenseController::class);
    //Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    //Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
//Route::post('/shipments/{shipment}/quick-update', [ShipmentController::class, 'quickUpdate']);
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/accounting/treasury-report', [AccountingController::class, 'treasuryReport'])->name('accounting.treasury-report');
Route::post('/shipments/{shipment}/quick-update', [ShipmentController::class, 'quickUpdate'])->name('shipments.quickUpdate');

    Route::resource('shipments', ShipmentController::class);
    Route::get('/products/print-barcodes', [\App\Http\Controllers\ProductController::class, 'printBarcodes'])->name('products.print.barcodes');
    Route::resource('products', ProductController::class);
    Route::resource('delivery-agents', DeliveryAgentController::class);
    Route::resource('shipment-statuses', ShipmentStatusController::class);
    Route::resource('shipping-companies', ShippingCompanyController::class);

    Route::get('shipments/export-print', [ShipmentExportController::class, 'exportPrint'])->name('shipments.export.print');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/shipments', [ReportController::class, 'shipments'])->name('shipments');
        Route::get('/shipments/excel', [ReportController::class, 'exportShipmentsExcel'])->name('shipments.excel');
        Route::get('/shipments/pdf', [ReportController::class, 'exportShipmentsPdf'])->name('shipments.pdf');

        Route::get('/expenses', [ExpensesReportController::class, 'index'])->name('expenses');
        Route::get('/expenses/excel', [ExpensesReportController::class, 'exportExcel'])->name('expenses.excel');
        Route::get('/expenses/pdf', [ExpensesReportController::class, 'exportPdf'])->name('expenses.pdf');

        Route::get('/collections/excel', [CollectionsReportController::class, 'exportExcel'])->name('collections.excel');
        Route::get('/collections/pdf', [CollectionsReportController::class, 'exportPdf'])->name('collections.pdf');

        Route::get('/treasury/excel', [ReportController::class, 'exportTreasuryExcel'])->name('treasury.excel');
        Route::get('/treasury/pdf', [ReportController::class, 'exportTreasuryPdf'])->name('treasury.pdf');
        Route::get('/treasury/pdf', [ReportController::class, 'exportTreasuryPdf'])->name('treasury.pdf');
    });

    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk_action');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

    Route::prefix('settings')->controller(SettingController::class)->name('settings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'update')->name('update');
        Route::get('notifications', 'notifications')->name('notifications');
        Route::post('notifications', 'updateNotifications')->name('notifications.update');
        Route::get('system', 'system')->name('system');
        Route::post('system', 'updateSystem')->name('system.update');
        Route::get('create-backup', 'createBackup')->name('create-backup');
    });

});


