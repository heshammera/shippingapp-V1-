# دليل المطور - نظام إدارة الشحنات

## نظرة عامة على النظام

نظام إدارة الشحنات هو تطبيق ويب متكامل مبني باستخدام إطار العمل Laravel، ومصمم لإدارة عمليات الشحن والتوصيل لشركات بيع الملابس أونلاين. يتبع النظام نمط التصميم MVC (Model-View-Controller) ويستخدم قاعدة بيانات MySQL لتخزين البيانات.

## المتطلبات التقنية

- PHP 8.1 أو أحدث
- MySQL 5.7 أو أحدث
- Composer
- Node.js و NPM
- خادم ويب (Apache أو Nginx)
- متصفح ويب حديث

## هيكل المشروع

```
shipping_app/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Exports/           # فئات تصدير البيانات
│   ├── Http/
│   │   ├── Controllers/   # وحدات التحكم
│   │   ├── Middleware/    # وسائط البرمجة
│   │   └── Requests/      # طلبات النماذج
│   ├── Imports/           # فئات استيراد البيانات
│   └── Models/            # نماذج البيانات
├── bootstrap/
├── config/
├── database/
│   ├── factories/         # مصانع البيانات للاختبارات
│   ├── migrations/        # ملفات هجرة قاعدة البيانات
│   └── seeders/          # بذور البيانات
├── public/
│   ├── css/
│   ├── js/
│   └── images/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/             # قوالب العرض
│       ├── accounting/
│       ├── collections/
│       ├── dashboard/
│       ├── delivery_agents/
│       ├── expenses/
│       ├── layouts/
│       ├── reports/
│       ├── roles/
│       ├── settings/
│       ├── shipments/
│       ├── shipping_companies/
│       └── users/
├── routes/
│   └── web.php            # تعريفات المسارات
├── storage/
├── tests/                 # اختبارات النظام
│   ├── Feature/
│   └── Unit/
└── vendor/
```

## وحدات النظام

### 1. وحدة إدارة شركات الشحن

#### النماذج
- `ShippingCompany.php`: نموذج شركة الشحن

#### وحدات التحكم
- `ShippingCompanyController.php`: وحدة تحكم شركات الشحن

#### قوالب العرض
- `shipping_companies/index.blade.php`: عرض قائمة شركات الشحن
- `shipping_companies/create.blade.php`: إنشاء شركة شحن جديدة
- `shipping_companies/edit.blade.php`: تعديل بيانات شركة شحن
- `shipping_companies/show.blade.php`: عرض تفاصيل شركة شحن

### 2. وحدة إدارة الشحنات

#### النماذج
- `Shipment.php`: نموذج الشحنة
- `ShipmentStatus.php`: نموذج حالة الشحنة

#### وحدات التحكم
- `ShipmentController.php`: وحدة تحكم الشحنات

#### فئات الاستيراد
- `ShipmentsImport.php`: فئة استيراد الشحنات من Excel

#### قوالب العرض
- `shipments/index.blade.php`: عرض قائمة الشحنات
- `shipments/create.blade.php`: إنشاء شحنة جديدة
- `shipments/edit.blade.php`: تعديل بيانات شحنة
- `shipments/show.blade.php`: عرض تفاصيل شحنة
- `shipments/import.blade.php`: استيراد الشحنات من Excel

### 3. وحدة إدارة المندوبين

#### النماذج
- `DeliveryAgent.php`: نموذج المندوب

#### وحدات التحكم
- `DeliveryAgentController.php`: وحدة تحكم المندوبين

#### قوالب العرض
- `delivery_agents/index.blade.php`: عرض قائمة المندوبين
- `delivery_agents/create.blade.php`: إنشاء مندوب جديد
- `delivery_agents/edit.blade.php`: تعديل بيانات مندوب
- `delivery_agents/show.blade.php`: عرض تفاصيل مندوب
- `delivery_agents/shipments.blade.php`: عرض شحنات المندوب

### 4. وحدة الحسابات (الخزنة)

#### النماذج
- `Collection.php`: نموذج التحصيل
- `Expense.php`: نموذج المصروف

#### وحدات التحكم
- `CollectionController.php`: وحدة تحكم التحصيلات
- `ExpenseController.php`: وحدة تحكم المصاريف
- `AccountingController.php`: وحدة تحكم الحسابات

#### قوالب العرض
- `collections/index.blade.php`: عرض قائمة التحصيلات
- `collections/create.blade.php`: إنشاء تحصيل جديد
- `collections/edit.blade.php`: تعديل بيانات تحصيل
- `collections/show.blade.php`: عرض تفاصيل تحصيل
- `collections/report.blade.php`: تقرير التحصيلات
- `expenses/index.blade.php`: عرض قائمة المصاريف
- `expenses/create.blade.php`: إنشاء مصروف جديد
- `expenses/edit.blade.php`: تعديل بيانات مصروف
- `expenses/show.blade.php`: عرض تفاصيل مصروف
- `expenses/report.blade.php`: تقرير المصاريف
- `accounting/index.blade.php`: لوحة الحسابات
- `accounting/treasury_report.blade.php`: تقرير الخزنة

### 5. وحدة التقارير والتصدير

#### فئات التصدير
- `ShipmentsExport.php`: فئة تصدير الشحنات
- `CollectionsExport.php`: فئة تصدير التحصيلات
- `ExpensesExport.php`: فئة تصدير المصاريف
- `TreasuryReportExport.php`: فئة تصدير تقرير الخزنة

#### وحدات التحكم
- `ReportController.php`: وحدة تحكم التقارير

#### قوالب العرض
- `reports/index.blade.php`: صفحة التقارير الرئيسية
- `reports/shipments.blade.php`: تقرير الشحنات
- `reports/shipments_pdf.blade.php`: قالب PDF لتقرير الشحنات
- `reports/collections_pdf.blade.php`: قالب PDF لتقرير التحصيلات
- `reports/expenses_pdf.blade.php`: قالب PDF لتقرير المصاريف
- `reports/treasury_pdf.blade.php`: قالب PDF لتقرير الخزنة

### 6. وحدة إعدادات النظام

#### النماذج
- `Setting.php`: نموذج الإعدادات

#### وحدات التحكم
- `SettingController.php`: وحدة تحكم الإعدادات

#### قوالب العرض
- `settings/index.blade.php`: الإعدادات العامة
- `settings/notifications.blade.php`: إعدادات الإشعارات
- `settings/system.blade.php`: إعدادات النظام

### 7. وحدة المستخدمين والصلاحيات

#### النماذج
- `User.php`: نموذج المستخدم
- `Role.php`: نموذج الدور
- `Permission.php`: نموذج الصلاحية

#### وحدات التحكم
- `UserController.php`: وحدة تحكم المستخدمين
- `RoleController.php`: وحدة تحكم الأدوار

#### وسائط البرمجة
- `CheckPermission.php`: وسيط التحقق من الصلاحيات

#### قوالب العرض
- `users/index.blade.php`: عرض قائمة المستخدمين
- `users/create.blade.php`: إنشاء مستخدم جديد
- `users/edit.blade.php`: تعديل بيانات مستخدم
- `users/show.blade.php`: عرض تفاصيل مستخدم
- `roles/index.blade.php`: عرض قائمة الأدوار
- `roles/create.blade.php`: إنشاء دور جديد
- `roles/edit.blade.php`: تعديل بيانات دور
- `roles/show.blade.php`: عرض تفاصيل دور

## العلاقات بين النماذج

### ShippingCompany
- `shipments()`: علاقة one-to-many مع Shipment
- `collections()`: علاقة one-to-many مع Collection

### Shipment
- `shippingCompany()`: علاقة many-to-one مع ShippingCompany
- `status()`: علاقة many-to-one مع ShipmentStatus
- `deliveryAgent()`: علاقة many-to-one مع DeliveryAgent
- `createdBy()`: علاقة many-to-one مع User

### DeliveryAgent
- `shipments()`: علاقة one-to-many مع Shipment

### Collection
- `shippingCompany()`: علاقة many-to-one مع ShippingCompany
- `createdBy()`: علاقة many-to-one مع User

### Expense
- `createdBy()`: علاقة many-to-one مع User

### User
- `roles()`: علاقة many-to-many مع Role
- `shipments()`: علاقة one-to-many مع Shipment
- `collections()`: علاقة one-to-many مع Collection
- `expenses()`: علاقة one-to-many مع Expense

### Role
- `permissions()`: علاقة many-to-many مع Permission
- `users()`: علاقة many-to-many مع User

## المسارات (Routes)

```php
// الصفحة الرئيسية
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// إدارة شركات الشحن
Route::resource('shipping-companies', ShippingCompanyController::class);

// إدارة الشحنات
Route::resource('shipments', ShipmentController::class);
Route::get('shipments/import/form', [ShipmentController::class, 'importForm'])->name('shipments.import.form');
Route::post('shipments/import', [ShipmentController::class, 'import'])->name('shipments.import');
Route::put('shipments/{shipment}/update-status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');

// إدارة المستخدمين
Route::resource('users', UserController::class);

// إدارة الأدوار والصلاحيات
Route::resource('roles', RoleController::class);

// إدارة المندوبين
Route::resource('delivery-agents', DeliveryAgentController::class);
Route::get('delivery-agents/{deliveryAgent}/shipments', [DeliveryAgentController::class, 'shipments'])->name('delivery-agents.shipments');

// إدارة التحصيلات
Route::resource('collections', CollectionController::class);
Route::get('collections-report', [CollectionController::class, 'report'])->name('collections.report');

// إدارة المصاريف
Route::resource('expenses', ExpenseController::class);
Route::get('expenses-report', [ExpenseController::class, 'report'])->name('expenses.report');

// إدارة الحسابات (الخزنة)
Route::get('accounting', [AccountingController::class, 'index'])->name('accounting.index');
Route::get('accounting/treasury-report', [AccountingController::class, 'treasuryReport'])->name('accounting.treasury-report');

// التقارير والتصدير
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/shipments', [ReportController::class, 'shipments'])->name('reports.shipments');
Route::get('reports/shipments/excel', [ReportController::class, 'exportShipmentsExcel'])->name('reports.shipments.excel');
Route::get('reports/shipments/pdf', [ReportController::class, 'exportShipmentsPdf'])->name('reports.shipments.pdf');
Route::get('reports/collections/excel', [ReportController::class, 'exportCollectionsExcel'])->name('reports.collections.excel');
Route::get('reports/collections/pdf', [ReportController::class, 'exportCollectionsPdf'])->name('reports.collections.pdf');
Route::get('reports/expenses/excel', [ReportController::class, 'exportExpensesExcel'])->name('reports.expenses.excel');
Route::get('reports/expenses/pdf', [ReportController::class, 'exportExpensesPdf'])->name('reports.expenses.pdf');
Route::get('reports/treasury/excel', [ReportController::class, 'exportTreasuryExcel'])->name('reports.treasury.excel');
Route::get('reports/treasury/pdf', [ReportController::class, 'exportTreasuryPdf'])->name('reports.treasury.pdf');

// إعدادات النظام
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
Route::get('settings/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
Route::post('settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.update-notifications');
Route::get('settings/system', [SettingController::class, 'system'])->name('settings.system');
Route::post('settings/system', [SettingController::class, 'updateSystem'])->name('settings.update-system');
Route::get('settings/create-backup', [SettingController::class, 'createBackup'])->name('settings.create-backup');
```

## نظام الصلاحيات

يستخدم النظام نموذج الأدوار والصلاحيات (Role-Permission) لإدارة صلاحيات المستخدمين. يتم تعريف الصلاحيات في جدول `permissions` وتجميعها في أدوار في جدول `roles`. يتم تعيين الأدوار للمستخدمين من خلال جدول العلاقة `role_user`.

### الصلاحيات المتاحة

- `view_dashboard`: عرض لوحة التحكم
- `manage_shipping_companies`: إدارة شركات الشحن
- `manage_shipments`: إدارة الشحنات
- `import_shipments`: استيراد الشحنات
- `update_shipment_status`: تحديث حالة الشحنة
- `manage_delivery_agents`: إدارة المندوبين
- `manage_collections`: إدارة التحصيلات
- `manage_expenses`: إدارة المصاريف
- `view_accounting`: عرض الحسابات
- `generate_reports`: إنشاء التقارير
- `manage_settings`: إدارة الإعدادات
- `manage_users`: إدارة المستخدمين
- `manage_roles`: إدارة الأدوار

### وسيط التحقق من الصلاحيات

يتم استخدام وسيط `CheckPermission` للتحقق من صلاحيات المستخدم قبل السماح له بالوصول إلى الصفحات المختلفة. يتم تسجيل هذا الوسيط في ملف `Kernel.php` وتطبيقه على المسارات المحمية.

```php
// app/Http/Middleware/CheckPermission.php
public function handle($request, Closure $next, $permission)
{
    if (!auth()->check() || !auth()->user()->hasPermission($permission)) {
        abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
    }

    return $next($request);
}
```

## استيراد وتصدير البيانات

### استيراد الشحنات من Excel

يستخدم النظام مكتبة `maatwebsite/excel` لاستيراد بيانات الشحنات من ملفات Excel. يتم تعريف فئة `ShipmentsImport` لتحديد كيفية قراءة البيانات من الملف وإدخالها في قاعدة البيانات.

```php
// app/Imports/ShipmentsImport.php
public function model(array $row)
{
    // تحويل بيانات الصف إلى نموذج Shipment
}
```

### تصدير التقارير

يتم استخدام مكتبتي `maatwebsite/excel` و `barryvdh/laravel-dompdf` لتصدير التقارير بصيغتي Excel و PDF على التوالي. يتم تعريف فئات التصدير المختلفة لكل نوع من التقارير.

```php
// app/Exports/ShipmentsExport.php
public function collection()
{
    // استرجاع بيانات الشحنات للتصدير
}
```

## إعدادات النظام

يستخدم النظام نموذج `Setting` لتخزين واسترجاع إعدادات النظام المختلفة. يوفر النموذج طرق مساعدة للحصول على قيم الإعدادات وتعيينها.

```php
// app/Models/Setting.php
public static function getValue($key, $default = null)
{
    $setting = self::where('key', $key)->first();
    return $setting ? $setting->value : $default;
}

public static function setValue($key, $value)
{
    $setting = self::updateOrCreate(
        ['key' => $key],
        ['value' => $value]
    );
    return $setting;
}
```

## الاختبارات

يتضمن النظام مجموعة شاملة من اختبارات الوحدة والتكامل للتأكد من صحة عمل جميع الوظائف. تم تنظيم الاختبارات في مجلد `tests` وتقسيمها إلى اختبارات وحدة واختبارات ميزات.

```php
// tests/Feature/ShipmentTest.php
public function test_index_shipments()
{
    // اختبار عرض قائمة الشحنات
}

public function test_store_shipment()
{
    // اختبار إنشاء شحنة جديدة
}
```

## التثبيت والإعداد

### متطلبات النظام

- PHP 8.1 أو أحدث
- MySQL 5.7 أو أحدث
- Composer
- Node.js و NPM
- خادم ويب (Apache أو Nginx)

### خطوات التثبيت

1. استنساخ المستودع:
   ```
   git clone https://github.com/example/shipping-management-system.git
   cd shipping-management-system
   ```

2. تثبيت اعتماديات PHP:
   ```
   composer install
   ```

3. تثبيت اعتماديات JavaScript:
   ```
   npm install
   npm run build
   ```

4. إنشاء ملف البيئة:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. تكوين قاعدة البيانات في ملف `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=shipping_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. تشغيل هجرات قاعدة البيانات:
   ```
   php artisan migrate
   ```

7. زرع البيانات الأولية:
   ```
   php artisan db:seed
   ```

8. تشغيل الخادم المحلي:
   ```
   php artisan serve
   ```

9. الوصول إلى النظام:
   افتح المتصفح وانتقل إلى `http://localhost:8000`

### بيانات تسجيل الدخول الافتراضية

- البريد الإلكتروني: admin@example.com
- كلمة المرور: password

## التخصيص والتوسيع

### إضافة حالات شحن جديدة

1. أضف سجلات جديدة إلى جدول `shipment_statuses`
2. قم بتحديث الألوان المرتبطة بالحالات في ملف `resources/views/shipments/index.blade.php`

### إضافة تقارير جديدة

1. أنشئ فئة تصدير جديدة في مجلد `app/Exports`
2. أضف طرق جديدة في `ReportController.php`
3. أنشئ قوالب عرض جديدة في مجلد `resources/views/reports`
4. أضف مسارات جديدة في ملف `routes/web.php`

### تخصيص واجهة المستخدم

1. قم بتعديل ملفات CSS في مجلد `resources/css`
2. قم بتعديل ملفات JavaScript في مجلد `resources/js`
3. قم بتعديل قوالب العرض في مجلد `resources/views`

## الصيانة والتحديث

### النسخ الاحتياطي

يوفر النظام وظيفة للنسخ الاحتياطي التلقائي والنسخ الاحتياطي اليدوي. يتم تخزين النسخ الاحتياطية في مجلد `storage/app/backups`.

### التحديثات

لتحديث النظام إلى أحدث إصدار:

1. استنساخ أحدث التغييرات:
   ```
   git pull origin main
   ```

2. تحديث اعتماديات PHP:
   ```
   composer update
   ```

3. تحديث اعتماديات JavaScript:
   ```
   npm update
   npm run build
   ```

4. تشغيل هجرات قاعدة البيانات الجديدة:
   ```
   php artisan migrate
   ```

## استكشاف الأخطاء وإصلاحها

### سجلات الأخطاء

يتم تخزين سجلات الأخطاء في مجلد `storage/logs`. يمكن الوصول إلى أحدث سجل من خلال:

```
tail -f storage/logs/laravel.log
```

### المشكلات الشائعة وحلولها

1. **خطأ في الاتصال بقاعدة البيانات**
   - تحقق من صحة بيانات الاتصال في ملف `.env`
   - تأكد من تشغيل خدمة MySQL

2. **مشكلات في تصاريح الملفات**
   - تأكد من أن مجلدات `storage` و `bootstrap/cache` قابلة للكتابة من قبل خادم الويب

3. **مشكلات في استيراد/تصدير Excel**
   - تأكد من تثبيت حزمة `php-zip`
   - تحقق من تنسيق ملف Excel المستورد

4. **مشكلات في إنشاء ملفات PDF**
   - تأكد من تثبيت حزمة `php-gd`
   - تحقق من تصاريح مجلد `storage/app/public`

## الموارد والمراجع

- [وثائق Laravel](https://laravel.com/docs)
- [وثائق Laravel Excel](https://docs.laravel-excel.com)
- [وثائق Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)
- [Bootstrap 5](https://getbootstrap.com/docs/5.0)
- [Chart.js](https://www.chartjs.org/docs)
