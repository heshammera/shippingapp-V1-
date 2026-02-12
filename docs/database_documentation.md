# توثيق قاعدة البيانات - نظام إدارة الشحنات

## نظرة عامة

يستخدم نظام إدارة الشحنات قاعدة بيانات MySQL لتخزين جميع البيانات. تم تصميم هيكل قاعدة البيانات لدعم جميع وظائف النظام بكفاءة، مع التركيز على العلاقات المناسبة بين الجداول والأداء الأمثل للاستعلامات.

## مخطط قاعدة البيانات

فيما يلي مخطط العلاقات بين الجداول الرئيسية في قاعدة البيانات:

```
users -----< shipments >------ shipping_companies
  |             |                     |
  |             |                     |
  v             v                     v
roles       shipment_statuses    collections
  |
  |
  v
permissions
```

## جداول قاعدة البيانات

### 1. جدول المستخدمين (users)

يخزن بيانات المستخدمين المسجلين في النظام.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| name | varchar(255) | اسم المستخدم |
| email | varchar(255) | البريد الإلكتروني (فريد) |
| email_verified_at | timestamp | تاريخ التحقق من البريد الإلكتروني |
| password | varchar(255) | كلمة المرور (مشفرة) |
| remember_token | varchar(100) | رمز تذكر تسجيل الدخول |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 2. جدول الأدوار (roles)

يخزن الأدوار المختلفة التي يمكن تعيينها للمستخدمين.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| name | varchar(255) | اسم الدور |
| description | text | وصف الدور |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 3. جدول الصلاحيات (permissions)

يخزن الصلاحيات المختلفة التي يمكن منحها للأدوار.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| name | varchar(255) | اسم الصلاحية |
| description | text | وصف الصلاحية |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 4. جدول علاقة الأدوار والصلاحيات (role_permission)

يخزن العلاقة بين الأدوار والصلاحيات (علاقة many-to-many).

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| role_id | bigint(20) unsigned | معرف الدور (مفتاح خارجي) |
| permission_id | bigint(20) unsigned | معرف الصلاحية (مفتاح خارجي) |

### 5. جدول علاقة المستخدمين والأدوار (role_user)

يخزن العلاقة بين المستخدمين والأدوار (علاقة many-to-many).

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| user_id | bigint(20) unsigned | معرف المستخدم (مفتاح خارجي) |
| role_id | bigint(20) unsigned | معرف الدور (مفتاح خارجي) |

### 6. جدول شركات الشحن (shipping_companies)

يخزن بيانات شركات الشحن.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| name | varchar(255) | اسم الشركة |
| phone | varchar(255) | رقم الهاتف |
| address | text | العنوان |
| email | varchar(255) | البريد الإلكتروني |
| notes | text | ملاحظات |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 7. جدول حالات الشحنات (shipment_statuses)

يخزن حالات الشحنات المختلفة.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| name | varchar(255) | اسم الحالة |
| color | varchar(255) | لون الحالة (للعرض في الواجهة) |
| description | text | وصف الحالة |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 8. جدول المندوبين (delivery_agents)

يخزن بيانات مندوبي التوصيل.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| name | varchar(255) | اسم المندوب |
| phone | varchar(255) | رقم الهاتف |
| address | text | العنوان |
| email | varchar(255) | البريد الإلكتروني |
| national_id | varchar(255) | الرقم القومي |
| commission_rate | decimal(8,2) | نسبة العمولة |
| notes | text | ملاحظات |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 9. جدول الشحنات (shipments)

يخزن بيانات الشحنات.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| tracking_number | varchar(255) | رقم التتبع |
| customer_name | varchar(255) | اسم العميل |
| customer_phone | varchar(255) | رقم هاتف العميل |
| customer_address | text | عنوان العميل |
| product_name | varchar(255) | اسم المنتج |
| quantity | int | الكمية |
| cost_price | decimal(10,2) | سعر التكلفة |
| selling_price | decimal(10,2) | سعر البيع |
| shipping_company_id | bigint(20) unsigned | معرف شركة الشحن (مفتاح خارجي) |
| status_id | bigint(20) unsigned | معرف حالة الشحنة (مفتاح خارجي) |
| delivery_agent_id | bigint(20) unsigned | معرف المندوب (مفتاح خارجي) |
| shipping_date | date | تاريخ الشحن |
| delivery_date | date | تاريخ التسليم |
| notes | text | ملاحظات |
| created_by | bigint(20) unsigned | معرف المستخدم الذي أنشأ الشحنة (مفتاح خارجي) |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 10. جدول التحصيلات (collections)

يخزن بيانات التحصيلات من شركات الشحن.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| shipping_company_id | bigint(20) unsigned | معرف شركة الشحن (مفتاح خارجي) |
| amount | decimal(10,2) | المبلغ المحصل |
| collection_date | date | تاريخ التحصيل |
| notes | text | ملاحظات |
| created_by | bigint(20) unsigned | معرف المستخدم الذي أنشأ التحصيل (مفتاح خارجي) |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 11. جدول المصاريف (expenses)

يخزن بيانات المصاريف.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| title | varchar(255) | عنوان المصروف |
| amount | decimal(10,2) | مبلغ المصروف |
| expense_date | date | تاريخ المصروف |
| notes | text | ملاحظات |
| created_by | bigint(20) unsigned | معرف المستخدم الذي أنشأ المصروف (مفتاح خارجي) |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

### 12. جدول الإعدادات (settings)

يخزن إعدادات النظام.

| اسم العمود | النوع | الوصف |
|------------|------|-------|
| id | bigint(20) unsigned | المعرف الفريد (المفتاح الأساسي) |
| key | varchar(255) | مفتاح الإعداد |
| value | text | قيمة الإعداد |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

## العلاقات بين الجداول

### علاقات المستخدمين والأدوار والصلاحيات

- **المستخدمين والأدوار**: علاقة many-to-many من خلال جدول `role_user`
- **الأدوار والصلاحيات**: علاقة many-to-many من خلال جدول `role_permission`

### علاقات الشحنات

- **الشحنات وشركات الشحن**: علاقة many-to-one (كل شحنة تنتمي لشركة شحن واحدة)
- **الشحنات وحالات الشحنات**: علاقة many-to-one (كل شحنة لها حالة واحدة)
- **الشحنات والمندوبين**: علاقة many-to-one (كل شحنة يمكن تعيينها لمندوب واحد)
- **الشحنات والمستخدمين**: علاقة many-to-one (كل شحنة تم إنشاؤها بواسطة مستخدم واحد)

### علاقات التحصيلات والمصاريف

- **التحصيلات وشركات الشحن**: علاقة many-to-one (كل تحصيل يرتبط بشركة شحن واحدة)
- **التحصيلات والمستخدمين**: علاقة many-to-one (كل تحصيل تم إنشاؤه بواسطة مستخدم واحد)
- **المصاريف والمستخدمين**: علاقة many-to-one (كل مصروف تم إنشاؤه بواسطة مستخدم واحد)

## ملفات هجرة قاعدة البيانات

تم إنشاء ملفات هجرة لكل جدول في قاعدة البيانات. هذه الملفات موجودة في مجلد `database/migrations` وتحدد بنية الجداول والعلاقات بينها.

### قائمة ملفات الهجرة

1. `2014_10_12_000000_create_users_table.php`
2. `2014_10_12_100000_create_password_reset_tokens_table.php`
3. `2019_08_19_000000_create_failed_jobs_table.php`
4. `2019_12_14_000001_create_personal_access_tokens_table.php`
5. `2025_04_26_180405_create_roles_table.php`
6. `2025_04_26_180405_create_permissions_table.php`
7. `2025_04_26_180405_create_role_permission_table.php`
8. `2025_04_26_180405_create_shipping_companies_table.php`
9. `2025_04_26_180406_create_shipment_statuses_table.php`
10. `2025_04_26_182939_create_delivery_agents_table.php`
11. `2025_04_26_180406_create_shipments_table.php`
12. `2025_04_26_180406_create_collections_table.php`
13. `2025_04_26_180406_create_expenses_table.php`
14. `2025_04_26_180406_create_settings_table.php`

## البيانات الأولية (Seeders)

تم إنشاء بذور بيانات لملء قاعدة البيانات بالبيانات الأولية اللازمة لتشغيل النظام. هذه البذور موجودة في مجلد `database/seeders`.

### قائمة البذور

1. `RoleSeeder.php`: إنشاء الأدوار الافتراضية (مدير، مشرف، مستخدم)
2. `PermissionSeeder.php`: إنشاء الصلاحيات الافتراضية
3. `UserSeeder.php`: إنشاء مستخدم المدير الافتراضي
4. `ShipmentStatusSeeder.php`: إنشاء حالات الشحنات الافتراضية (قيد الانتظار، تم التسليم، مرتجع، عُهدة/تدوير)
5. `SettingSeeder.php`: إنشاء الإعدادات الافتراضية للنظام

## استعلامات قاعدة البيانات الشائعة

### استعلامات الشحنات

#### الحصول على جميع الشحنات مع العلاقات

```sql
SELECT s.*, sc.name as company_name, ss.name as status_name, ss.color as status_color, 
       da.name as agent_name, u.name as created_by_name
FROM shipments s
LEFT JOIN shipping_companies sc ON s.shipping_company_id = sc.id
LEFT JOIN shipment_statuses ss ON s.status_id = ss.id
LEFT JOIN delivery_agents da ON s.delivery_agent_id = da.id
LEFT JOIN users u ON s.created_by = u.id
ORDER BY s.created_at DESC;
```

#### البحث عن الشحنات حسب معايير متعددة

```sql
SELECT s.*, sc.name as company_name, ss.name as status_name
FROM shipments s
LEFT JOIN shipping_companies sc ON s.shipping_company_id = sc.id
LEFT JOIN shipment_statuses ss ON s.status_id = ss.id
WHERE (s.tracking_number LIKE '%search_term%' OR s.customer_name LIKE '%search_term%')
  AND (s.shipping_company_id = company_id OR company_id IS NULL)
  AND (s.status_id = status_id OR status_id IS NULL)
  AND (s.delivery_agent_id = agent_id OR agent_id IS NULL)
  AND (s.shipping_date BETWEEN start_date AND end_date OR start_date IS NULL OR end_date IS NULL)
ORDER BY s.created_at DESC;
```

### استعلامات التحصيلات والمصاريف

#### الحصول على إجمالي التحصيلات حسب شركة الشحن

```sql
SELECT sc.name as company_name, SUM(c.amount) as total_amount
FROM collections c
JOIN shipping_companies sc ON c.shipping_company_id = sc.id
WHERE c.collection_date BETWEEN start_date AND end_date
GROUP BY sc.id, sc.name
ORDER BY total_amount DESC;
```

#### الحصول على إجمالي المصاريف حسب الشهر

```sql
SELECT DATE_FORMAT(e.expense_date, '%Y-%m') as month, SUM(e.amount) as total_amount
FROM expenses e
WHERE e.expense_date BETWEEN start_date AND end_date
GROUP BY DATE_FORMAT(e.expense_date, '%Y-%m')
ORDER BY month;
```

#### الحصول على تقرير الخزنة (التحصيلات والمصاريف)

```sql
SELECT t.date, t.type, t.description, t.amount, 
       @running_total := @running_total + (CASE WHEN t.type = 'collection' THEN t.amount ELSE -t.amount END) as balance
FROM (
    SELECT c.collection_date as date, 'collection' as type, 
           CONCAT('تحصيل من ', sc.name) as description, c.amount
    FROM collections c
    JOIN shipping_companies sc ON c.shipping_company_id = sc.id
    WHERE c.collection_date BETWEEN start_date AND end_date
    
    UNION ALL
    
    SELECT e.expense_date as date, 'expense' as type, 
           e.title as description, e.amount
    FROM expenses e
    WHERE e.expense_date BETWEEN start_date AND end_date
) t, (SELECT @running_total := 0) r
ORDER BY t.date, t.type;
```

### استعلامات لوحة التحكم

#### الحصول على إحصائيات عامة

```sql
-- إجمالي عدد الشحنات
SELECT COUNT(*) as total_shipments FROM shipments;

-- عدد الشحنات حسب الحالة
SELECT ss.name as status_name, ss.color as status_color, COUNT(s.id) as count
FROM shipment_statuses ss
LEFT JOIN shipments s ON ss.id = s.status_id
GROUP BY ss.id, ss.name, ss.color;

-- عدد الشحنات حسب شركة الشحن
SELECT sc.name as company_name, COUNT(s.id) as count
FROM shipping_companies sc
LEFT JOIN shipments s ON sc.id = s.shipping_company_id
GROUP BY sc.id, sc.name;

-- إجمالي المبالغ المحصلة
SELECT SUM(amount) as total_collected FROM collections;

-- إجمالي المصاريف
SELECT SUM(amount) as total_expenses FROM expenses;
```

## أمان قاعدة البيانات

### التحقق من الصحة

يتم التحقق من صحة جميع البيانات المدخلة قبل حفظها في قاعدة البيانات باستخدام فئات الطلبات المخصصة في Laravel.

### الحماية من هجمات حقن SQL

يستخدم النظام استعلامات معلمة (parameterized queries) من خلال Eloquent ORM و Query Builder في Laravel، مما يوفر حماية تلقائية ضد هجمات حقن SQL.

### النسخ الاحتياطي

يوفر النظام وظيفة للنسخ الاحتياطي التلقائي والنسخ الاحتياطي اليدوي لقاعدة البيانات. يتم تخزين النسخ الاحتياطية في مجلد `storage/app/backups`.

## تحسين أداء قاعدة البيانات

### الفهارس

تم إنشاء فهارس على الأعمدة التي يتم البحث عنها بشكل متكرر لتحسين أداء الاستعلامات:

- فهرس على `tracking_number` في جدول `shipments`
- فهرس على `shipping_company_id` في جدول `shipments`
- فهرس على `status_id` في جدول `shipments`
- فهرس على `delivery_agent_id` في جدول `shipments`
- فهرس على `shipping_date` في جدول `shipments`
- فهرس على `shipping_company_id` في جدول `collections`
- فهرس على `collection_date` في جدول `collections`
- فهرس على `expense_date` في جدول `expenses`
- فهرس على `key` في جدول `settings`

### تحسين الاستعلامات

تم تحسين الاستعلامات المعقدة باستخدام تقنيات مثل:

- استخدام `eager loading` لتحميل العلاقات بكفاءة
- استخدام `chunk` للتعامل مع مجموعات البيانات الكبيرة
- استخدام `pagination` لتقسيم النتائج إلى صفحات

## الترحيل والتحديث

### ترحيل قاعدة البيانات

لترحيل قاعدة البيانات إلى بيئة جديدة:

1. إنشاء قاعدة بيانات جديدة
2. تكوين اتصال قاعدة البيانات في ملف `.env`
3. تشغيل أمر الهجرة:
   ```
   php artisan migrate
   ```
4. زرع البيانات الأولية:
   ```
   php artisan db:seed
   ```

### تحديث هيكل قاعدة البيانات

لإجراء تغييرات على هيكل قاعدة البيانات:

1. إنشاء ملف هجرة جديد:
   ```
   php artisan make:migration migration_name
   ```
2. تعريف التغييرات في ملف الهجرة
3. تشغيل الهجرة:
   ```
   php artisan migrate
   ```

## استكشاف الأخطاء وإصلاحها

### مشكلات شائعة وحلولها

1. **خطأ في الاتصال بقاعدة البيانات**
   - تحقق من صحة بيانات الاتصال في ملف `.env`
   - تأكد من تشغيل خدمة MySQL
   - تحقق من وجود قاعدة البيانات وصلاحيات المستخدم

2. **خطأ في تشغيل الهجرات**
   - تحقق من تسلسل الهجرات وعدم وجود تعارضات
   - استخدم `php artisan migrate:fresh` لإعادة إنشاء جميع الجداول (سيتم حذف جميع البيانات)

3. **بطء في أداء الاستعلامات**
   - تحقق من وجود الفهارس المناسبة
   - استخدم `EXPLAIN` لتحليل الاستعلامات
   - تحسين الاستعلامات المعقدة

## الموارد والمراجع

- [وثائق Laravel - قواعد البيانات](https://laravel.com/docs/10.x/database)
- [وثائق Laravel - Eloquent ORM](https://laravel.com/docs/10.x/eloquent)
- [وثائق Laravel - هجرات قاعدة البيانات](https://laravel.com/docs/10.x/migrations)
- [وثائق MySQL](https://dev.mysql.com/doc/)
