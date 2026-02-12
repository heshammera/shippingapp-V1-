<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .warning { color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; padding: 10px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>مرحباً {{ $user->name }}،</h2>
        
        <p>نود تنبيهك بأن صلاحية حسابك في النظام ستنتهي قريباً.</p>
        
        <div class="warning">
            <strong>الأيام المتبقية:</strong> {{ $daysLeft }} يوم
        </div>
        
        <p>يرجى التواصل مع الإدارة لتجديد الاشتراك وضمان استمرار الخدمة دون انقطاع.</p>
        
        <p>شكراً لاستخدامكم نظام إدارة الشحنات.</p>
        
        <hr>
        <small style="color: #666;">تم إرسال هذا البريد تلقائياً من النظام.</small>
    </div>
</body>
</html>
