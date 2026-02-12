# دليل التثبيت والتشغيل - نظام إدارة الشحنات

## متطلبات النظام

قبل تثبيت نظام إدارة الشحنات، تأكد من توفر المتطلبات التالية:

### متطلبات الخادم
- PHP 8.1 أو أحدث
- MySQL 5.7 أو أحدث
- خادم ويب (Apache أو Nginx)
- Composer
- Node.js و NPM

### حزم PHP المطلوبة
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Zip PHP Extension
- GD PHP Extension (لإنشاء ملفات PDF)

## خطوات التثبيت

### 1. تحميل النظام

قم بتحميل ملفات النظام من المستودع أو من الأرشيف المقدم:

```bash
git clone https://github.com/example/shipping-management-system.git
cd shipping-management-system/shipping_app
```

أو قم بفك ضغط الأرشيف المقدم:

```bash
unzip shipping-management-system.zip
cd shipping-management-system/shipping_app
```

### 2. تثبيت اعتماديات PHP

قم بتثبيت اعتماديات PHP باستخدام Composer:

```bash
composer install
```

### 3. تثبيت اعتماديات JavaScript

قم بتثبيت اعتماديات JavaScript وبناء الأصول:

```bash
npm install
npm run build
```

### 4. إعداد ملف البيئة

قم بنسخ ملف البيئة النموذجي وإنشاء مفتاح التطبيق:

```bash
cp .env.example .env
php artisan key:generate
```

### 5. تكوين قاعدة البيانات

قم بتعديل ملف `.env` لتكوين اتصال قاعدة البيانات:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shipping_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

استبدل `shipping_db` باسم قاعدة البيانات التي أنشأتها، و`root` باسم مستخدم قاعدة البيانات، و`your_password` بكلمة المرور الخاصة بمستخدم قاعدة البيانات.

### 6. إنشاء قاعدة البيانات

قم بإنشاء قاعدة بيانات جديدة باستخدام MySQL:

```sql
CREATE DATABASE shipping_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. تشغيل هجرات قاعدة البيانات

قم بإنشاء جداول قاعدة البيانات:

```bash
php artisan migrate
```

### 8. زرع البيانات الأولية

قم بزرع البيانات الأولية في قاعدة البيانات:

```bash
php artisan db:seed
```

### 9. إعداد مجلدات التخزين

قم بإنشاء رابط رمزي لمجلد التخزين:

```bash
php artisan storage:link
```

قم بضبط تصاريح المجلدات:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 10. تكوين خادم الويب

#### Apache

قم بإنشاء ملف تكوين Apache جديد:

```apache
<VirtualHost *:80>
    ServerName shipping.example.com
    DocumentRoot /path/to/shipping-management-system/shipping_app/public

    <Directory /path/to/shipping-management-system/shipping_app/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/shipping-error.log
    CustomLog ${APACHE_LOG_DIR}/shipping-access.log combined
</VirtualHost>
```

استبدل `/path/to/shipping-management-system` بالمسار الكامل لمجلد النظام، و`shipping.example.com` باسم النطاق الذي ستستخدمه.

قم بتفعيل الموقع وإعادة تشغيل Apache:

```bash
sudo a2ensite shipping.conf
sudo systemctl restart apache2
```

#### Nginx

قم بإنشاء ملف تكوين Nginx جديد:

```nginx
server {
    listen 80;
    server_name shipping.example.com;
    root /path/to/shipping-management-system/shipping_app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

استبدل `/path/to/shipping-management-system` بالمسار الكامل لمجلد النظام، و`shipping.example.com` باسم النطاق الذي ستستخدمه، و`php8.1-fpm.sock` بإصدار PHP-FPM المثبت لديك.

قم بتفعيل الموقع وإعادة تشغيل Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/shipping.conf /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

### 11. الوصول إلى النظام

افتح متصفح الويب وانتقل إلى عنوان النظام (على سبيل المثال، `http://shipping.example.com` أو `http://localhost`).

استخدم بيانات تسجيل الدخول الافتراضية:
- البريد الإلكتروني: admin@example.com
- كلمة المرور: password

قم بتغيير كلمة المرور الافتراضية فور تسجيل الدخول.

## إعدادات ما بعد التثبيت

### 1. تكوين إعدادات النظام

بعد تسجيل الدخول، انتقل إلى صفحة الإعدادات لتكوين:
- معلومات الشركة
- العملة ونسبة الضريبة
- الحالة الافتراضية للشحنات الجديدة
- إعدادات النظام العامة

### 2. إعداد النسخ الاحتياطي التلقائي

لإعداد النسخ الاحتياطي التلقائي، قم بإضافة مهمة cron:

```bash
crontab -e
```

أضف السطر التالي لإنشاء نسخة احتياطية يومية في الساعة 1 صباحًا:

```
0 1 * * * cd /path/to/shipping-management-system/shipping_app && php artisan backup:run
```

### 3. إعداد إرسال البريد الإلكتروني

قم بتكوين إعدادات البريد الإلكتروني في ملف `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. إعداد خدمة الرسائل القصيرة

إذا كنت ترغب في استخدام خدمة الرسائل القصيرة، قم بتكوين إعدادات SMS في صفحة إعدادات الإشعارات:
- API Key
- معرف المرسل
- نموذج الرسالة

## تحديث النظام

لتحديث النظام إلى أحدث إصدار:

1. قم بتحميل أحدث الملفات:
   ```bash
   git pull origin main
   ```

2. قم بتحديث اعتماديات PHP:
   ```bash
   composer update
   ```

3. قم بتحديث اعتماديات JavaScript:
   ```bash
   npm update
   npm run build
   ```

4. قم بتشغيل هجرات قاعدة البيانات الجديدة:
   ```bash
   php artisan migrate
   ```

5. قم بمسح ذاكرة التخزين المؤقت:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

## استكشاف الأخطاء وإصلاحها

### مشكلات شائعة وحلولها

#### 1. خطأ في الاتصال بقاعدة البيانات

**المشكلة**: لا يمكن الاتصال بقاعدة البيانات.

**الحل**:
- تحقق من صحة بيانات الاتصال في ملف `.env`
- تأكد من تشغيل خدمة MySQL
- تأكد من وجود قاعدة البيانات وصلاحيات المستخدم

#### 2. مشكلات في تصاريح الملفات

**المشكلة**: خطأ "Permission denied" عند محاولة الكتابة إلى مجلدات التخزين.

**الحل**:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 3. الصفحة البيضاء

**المشكلة**: تظهر صفحة بيضاء بدلاً من النظام.

**الحل**:
- تحقق من سجلات الأخطاء في `storage/logs/laravel.log`
- تأكد من تثبيت جميع حزم PHP المطلوبة
- تأكد من إنشاء مفتاح التطبيق باستخدام `php artisan key:generate`

#### 4. مشكلات في استيراد/تصدير Excel

**المشكلة**: خطأ عند محاولة استيراد أو تصدير ملفات Excel.

**الحل**:
- تأكد من تثبيت حزمة `php-zip`
- تحقق من تنسيق ملف Excel المستورد

#### 5. مشكلات في إنشاء ملفات PDF

**المشكلة**: خطأ عند محاولة إنشاء ملفات PDF.

**الحل**:
- تأكد من تثبيت حزمة `php-gd`
- تحقق من تصاريح مجلد `storage/app/public`

## الدعم الفني

إذا واجهتك أي مشكلة أو كان لديك أي استفسار، يرجى التواصل مع فريق الدعم الفني:

- البريد الإلكتروني: support@example.com
- رقم الهاتف: 01234567890
- ساعات العمل: من الأحد إلى الخميس، 9 صباحًا - 5 مساءً
