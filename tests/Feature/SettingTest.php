<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Setting;
use App\Models\User;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار عرض صفحة الإعدادات العامة
     */
    public function test_index_settings()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض الإعدادات للاختبار
        Setting::setValue('company_name', 'شركة الملابس أونلاين للاختبار');
        Setting::setValue('currency', 'جنيه');

        // زيارة صفحة الإعدادات العامة
        $response = $this->get(route('settings.index'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار تحديث الإعدادات العامة
     */
    public function test_update_settings()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء حالة شحنة للاختبار
        $status = \App\Models\ShipmentStatus::factory()->create();

        // بيانات التحديث
        $data = [
            'company_name' => 'شركة الملابس أونلاين المحدثة',
            'company_address' => 'عنوان الشركة للاختبار',
            'company_phone' => '01234567890',
            'company_email' => 'test@company.com',
            'currency' => 'دولار',
            'tax_rate' => '15',
            'default_status_id' => $status->id,
        ];

        // إرسال طلب تحديث الإعدادات
        $response = $this->post(route('settings.update'), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('settings.index'));
        
        // التحقق من تحديث الإعدادات في قاعدة البيانات
        $this->assertEquals('شركة الملابس أونلاين المحدثة', Setting::getValue('company_name'));
        $this->assertEquals('دولار', Setting::getValue('currency'));
        $this->assertEquals('15', Setting::getValue('tax_rate'));
    }

    /**
     * اختبار عرض صفحة إعدادات الإشعارات
     */
    public function test_notifications_settings()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض الإعدادات للاختبار
        Setting::setValue('enable_sms_notifications', '1');
        Setting::setValue('enable_email_notifications', '0');

        // زيارة صفحة إعدادات الإشعارات
        $response = $this->get(route('settings.notifications'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار تحديث إعدادات الإشعارات
     */
    public function test_update_notifications_settings()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // بيانات التحديث
        $data = [
            'enable_sms_notifications' => '1',
            'enable_email_notifications' => '1',
            'sms_api_key' => 'api_key_test',
            'sms_sender_id' => 'SENDER',
            'smtp_host' => 'smtp.test.com',
            'smtp_port' => '587',
            'smtp_username' => 'test@test.com',
            'smtp_password' => 'password',
            'smtp_encryption' => 'tls',
            'smtp_from_address' => 'from@test.com',
            'smtp_from_name' => 'Test Sender',
            'notification_status_change' => '1',
            'notification_new_shipment' => '1',
            'notification_delivery_date' => '0',
        ];

        // إرسال طلب تحديث إعدادات الإشعارات
        $response = $this->post(route('settings.update-notifications'), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('settings.notifications'));
        
        // التحقق من تحديث الإعدادات في قاعدة البيانات
        $this->assertEquals('1', Setting::getValue('enable_sms_notifications'));
        $this->assertEquals('1', Setting::getValue('enable_email_notifications'));
        $this->assertEquals('api_key_test', Setting::getValue('sms_api_key'));
        $this->assertEquals('0', Setting::getValue('notification_delivery_date'));
    }

    /**
     * اختبار عرض صفحة إعدادات النظام
     */
    public function test_system_settings()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض الإعدادات للاختبار
        Setting::setValue('pagination_limit', '15');
        Setting::setValue('date_format', 'Y-m-d');

        // زيارة صفحة إعدادات النظام
        $response = $this->get(route('settings.system'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار تحديث إعدادات النظام
     */
    public function test_update_system_settings()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // بيانات التحديث
        $data = [
            'pagination_limit' => '20',
            'date_format' => 'd-m-Y',
            'time_format' => 'h:i A',
            'default_language' => 'ar',
            'enable_activity_log' => '1',
            'backup_enabled' => '1',
            'backup_frequency' => 'weekly',
            'backup_retention' => '14',
        ];

        // إرسال طلب تحديث إعدادات النظام
        $response = $this->post(route('settings.update-system'), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('settings.system'));
        
        // التحقق من تحديث الإعدادات في قاعدة البيانات
        $this->assertEquals('20', Setting::getValue('pagination_limit'));
        $this->assertEquals('d-m-Y', Setting::getValue('date_format'));
        $this->assertEquals('weekly', Setting::getValue('backup_frequency'));
    }

    /**
     * اختبار إنشاء نسخة احتياطية
     */
    public function test_create_backup()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // زيارة صفحة إنشاء نسخة احتياطية
        $response = $this->get(route('settings.create-backup'));

        // التحقق من إعادة التوجيه بعد إنشاء النسخة الاحتياطية
        $response->assertRedirect(route('settings.system'));
    }
}
