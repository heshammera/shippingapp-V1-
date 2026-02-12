# دليل نشر النظام على استضافة GoDaddy

هذا الدليل يشرح خطوات نشر نظام إدارة الشحنات على استضافة GoDaddy بالتفصيل.

## المتطلبات المسبقة

قبل البدء في عملية النشر، تأكد من توفر ما يلي:

1. حساب استضافة على GoDaddy مع خطة تدعم PHP و MySQL
2. تأكد من أن الاستضافة تدعم:
   - PHP 8.1 أو أحدث
   - MySQL 5.7 أو أحدث
   - دعم لـ Composer
   - الحزم المطلوبة لـ PHP (BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, Zip, GD)

## خطوات النشر

### 1. تجهيز ملفات المشروع

1. قم بإنشاء نسخة من المشروع للإنتاج:
   ```bash
   cd /home/ubuntu/shipping_management_system
   cp -r shipping_app shipping_app_production
   cd shipping_app_production
   ```

2. قم بتثبيت اعتماديات PHP للإنتاج:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. قم بتثبيت وبناء أصول JavaScript:
   ```bash
   npm install
   npm run build
   ```

4. قم بإنشاء ملف `.env` للإنتاج:
   ```bash
   cp .env.example .env
   ```

5. قم بتعديل ملف `.env` بإعدادات الإنتاج:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_CONNECTION=mysql
   DB_HOST=your-godaddy-db-host
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

6. قم بإنشاء مفتاح التطبيق:
   ```bash
   php artisan key:generate
   ```

7. قم بإزالة الملفات غير الضرورية:
   ```bash
   rm -rf node_modules
   rm -rf tests
   ```

8. قم بضغط المشروع:
   ```bash
   cd ..
   zip -r shipping_app_production.zip shipping_app_production
   ```

### 2. الوصول إلى لوحة تحكم GoDaddy

1. قم بتسجيل الدخول إلى حساب GoDaddy الخاص بك
2. انتقل إلى "My Products" (منتجاتي)
3. اختر خدمة الاستضافة التي تريد استخدامها
4. انقر على "Manage" (إدارة)

### 3. إعداد قاعدة البيانات

1. في لوحة تحكم GoDaddy، انتقل إلى "MySQL Databases" (قواعد بيانات MySQL)
2. انقر على "Create Database" (إنشاء قاعدة بيانات)
3. أدخل اسم قاعدة البيانات واسم المستخدم وكلمة المرور
4. احفظ معلومات الاتصال بقاعدة البيانات (اسم المضيف، اسم قاعدة البيانات، اسم المستخدم، كلمة المرور)

### 4. رفع ملفات المشروع

#### باستخدام مدير الملفات في لوحة تحكم GoDaddy

1. في لوحة تحكم GoDaddy، انتقل إلى "File Manager" (مدير الملفات)
2. انتقل إلى المجلد الجذر للموقع (عادة ما يكون `/public_html`)
3. قم بحذف أي ملفات موجودة إذا كنت تريد استبدالها
4. انقر على "Upload Files" (رفع ملفات)
5. قم برفع ملف `shipping_app_production.zip`
6. بعد اكتمال الرفع، قم بفك ضغط الملف
7. انقل محتويات مجلد `shipping_app_production/public` إلى المجلد الجذر `/public_html`
8. انقل باقي محتويات مجلد `shipping_app_production` إلى مجلد خارج المجلد الجذر (مثل `/shipping_app`)

#### باستخدام FTP

1. استخدم برنامج FTP مثل FileZilla للاتصال بخادم GoDaddy
2. استخدم معلومات FTP المقدمة من GoDaddy (المضيف، اسم المستخدم، كلمة المرور)
3. انتقل إلى المجلد الجذر للموقع (عادة ما يكون `/public_html`)
4. قم بحذف أي ملفات موجودة إذا كنت تريد استبدالها
5. قم برفع ملف `shipping_app_production.zip`
6. بعد اكتمال الرفع، استخدم أداة استخراج الملفات المضغوطة في لوحة تحكم GoDaddy لفك ضغط الملف
7. انقل محتويات مجلد `shipping_app_production/public` إلى المجلد الجذر `/public_html`
8. انقل باقي محتويات مجلد `shipping_app_production` إلى مجلد خارج المجلد الجذر (مثل `/shipping_app`)

### 5. تعديل ملف index.php

بعد نقل الملفات، يجب تعديل ملف `index.php` في المجلد الجذر `/public_html` لتحديث المسارات:

1. افتح ملف `index.php` للتحرير
2. قم بتغيير المسارات لتشير إلى المجلد الجديد:

```php
// قبل التعديل
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// بعد التعديل
require __DIR__.'/../shipping_app/vendor/autoload.php';
$app = require_once __DIR__.'/../shipping_app/bootstrap/app.php';
```

### 6. تكوين ملف .htaccess

تأكد من وجود ملف `.htaccess` في المجلد الجذر `/public_html` بالمحتوى التالي:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 7. تكوين قاعدة البيانات

1. قم بتحديث ملف `.env` في المجلد `/shipping_app` بمعلومات قاعدة البيانات التي أنشأتها:
   ```
   DB_CONNECTION=mysql
   DB_HOST=your-godaddy-db-host
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

2. قم بتشغيل هجرات قاعدة البيانات باستخدام SSH (إذا كان متاحًا) أو phpMyAdmin:

   #### باستخدام SSH (إذا كان متاحًا على استضافة GoDaddy)
   ```bash
   cd /shipping_app
   php artisan migrate
   php artisan db:seed
   ```

   #### باستخدام phpMyAdmin
   1. في لوحة تحكم GoDaddy، انتقل إلى "phpMyAdmin"
   2. اختر قاعدة البيانات التي أنشأتها
   3. انتقل إلى علامة التبويب "Import" (استيراد)
   4. قم باستيراد ملف SQL الذي يحتوي على هيكل قاعدة البيانات والبيانات الأولية
      (يمكنك إنشاء هذا الملف باستخدام الأمر `php artisan schema:dump` على جهازك المحلي)

### 8. ضبط تصاريح الملفات

1. قم بضبط تصاريح المجلدات التي تحتاج إلى الكتابة:
   - `/shipping_app/storage`
   - `/shipping_app/bootstrap/cache`

2. يمكنك ضبط التصاريح باستخدام مدير الملفات في لوحة تحكم GoDaddy:
   - انتقل إلى المجلد المطلوب
   - انقر بزر الماوس الأيمن واختر "Permissions" (التصاريح)
   - اضبط التصاريح على 755 للمجلدات و 644 للملفات

### 9. إنشاء رابط رمزي للتخزين

إذا كان SSH متاحًا على استضافة GoDaddy:

```bash
cd /public_html
ln -s ../shipping_app/storage/app/public storage
```

إذا لم يكن SSH متاحًا، يمكنك إنشاء مجلد `storage` في المجلد الجذر ونسخ الملفات العامة إليه.

### 10. تكوين الجدولة (Cron Jobs)

لإعداد المهام المجدولة مثل النسخ الاحتياطي التلقائي:

1. في لوحة تحكم GoDaddy، انتقل إلى "Cron Jobs" (المهام المجدولة)
2. أضف مهمة جديدة لتشغيل الأمر التالي كل يوم في الساعة 1 صباحًا:
   ```
   php /shipping_app/artisan schedule:run
   ```

### 11. اختبار النظام

1. افتح متصفح الويب وانتقل إلى عنوان موقعك (على سبيل المثال، `https://your-domain.com`)
2. تأكد من أن النظام يعمل بشكل صحيح
3. قم بتسجيل الدخول باستخدام بيانات تسجيل الدخول الافتراضية:
   - البريد الإلكتروني: admin@example.com
   - كلمة المرور: password
4. قم بتغيير كلمة المرور الافتراضية فور تسجيل الدخول

## استكشاف الأخطاء وإصلاحها

### 1. خطأ 500 (Internal Server Error)

**المشكلة**: يظهر خطأ 500 عند محاولة الوصول إلى الموقع.

**الحل**:
- تحقق من سجلات الأخطاء في مجلد `/shipping_app/storage/logs`
- تأكد من صحة معلومات قاعدة البيانات في ملف `.env`
- تأكد من ضبط تصاريح الملفات بشكل صحيح
- تأكد من تثبيت جميع حزم PHP المطلوبة

### 2. صفحة بيضاء

**المشكلة**: تظهر صفحة بيضاء بدلاً من النظام.

**الحل**:
- تحقق من تفعيل وضع التصحيح مؤقتًا بتغيير `APP_DEBUG=true` في ملف `.env`
- تحقق من سجلات الأخطاء في مجلد `/shipping_app/storage/logs`
- تأكد من إنشاء مفتاح التطبيق باستخدام `php artisan key:generate`

### 3. مشكلات في الوصول إلى الأصول (CSS, JS, Images)

**المشكلة**: لا تظهر الأنماط أو النصوص البرمجية أو الصور بشكل صحيح.

**الحل**:
- تأكد من إنشاء رابط رمزي للتخزين بشكل صحيح
- تحقق من مسارات الأصول في ملفات CSS و JS
- تأكد من ضبط `APP_URL` بشكل صحيح في ملف `.env`

### 4. مشكلات في تسجيل الدخول

**المشكلة**: لا يمكن تسجيل الدخول إلى النظام.

**الحل**:
- تأكد من إنشاء جداول قاعدة البيانات بشكل صحيح
- تأكد من زرع البيانات الأولية بما في ذلك حساب المدير
- تحقق من تكوين جلسات PHP بشكل صحيح

## ملاحظات إضافية

### تحسين الأداء

لتحسين أداء النظام على استضافة GoDaddy:

1. قم بتفعيل التخزين المؤقت:
   ```
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   ```

2. قم بتحسين تكوين OPcache في ملف `.htaccess`:
   ```apache
   <IfModule mod_php7.c>
       php_value opcache.enable 1
       php_value opcache.memory_consumption 128
       php_value opcache.interned_strings_buffer 8
       php_value opcache.max_accelerated_files 4000
       php_value opcache.revalidate_freq 60
       php_value opcache.fast_shutdown 1
   </IfModule>
   ```

### الأمان

لتعزيز أمان النظام:

1. تأكد من تعيين `APP_DEBUG=false` في ملف `.env` في بيئة الإنتاج
2. استخدم HTTPS لموقعك
3. قم بتغيير كلمة المرور الافتراضية فور تسجيل الدخول
4. قم بتحديث النظام بانتظام

### النسخ الاحتياطي

قم بإعداد نسخ احتياطية منتظمة لقاعدة البيانات والملفات:

1. استخدم ميزة النسخ الاحتياطي المدمجة في لوحة تحكم GoDaddy
2. قم بإعداد مهمة مجدولة لإنشاء نسخة احتياطية تلقائية باستخدام الأمر:
   ```
   php /shipping_app/artisan backup:run
   ```

## الدعم الفني

إذا واجهتك أي مشكلة أو كان لديك أي استفسار، يرجى التواصل مع فريق الدعم الفني:

- البريد الإلكتروني: support@example.com
- رقم الهاتف: 01234567890
- ساعات العمل: من الأحد إلى الخميس، 9 صباحًا - 5 مساءً
