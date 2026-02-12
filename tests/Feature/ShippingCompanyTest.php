<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ShippingCompany;
use App\Models\User;

class ShippingCompanyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار عرض قائمة شركات الشحن
     */
    public function test_index_shipping_companies()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض شركات الشحن للاختبار
        ShippingCompany::factory()->count(3)->create();

        // زيارة صفحة قائمة شركات الشحن
        $response = $this->get(route('shipping-companies.index'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على شركات الشحن
        $response->assertViewHas('shippingCompanies');
    }

    /**
     * اختبار إنشاء شركة شحن جديدة
     */
    public function test_create_shipping_company()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // زيارة صفحة إنشاء شركة شحن جديدة
        $response = $this->get(route('shipping-companies.create'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار حفظ شركة شحن جديدة
     */
    public function test_store_shipping_company()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // بيانات شركة الشحن الجديدة
        $data = [
            'name' => 'شركة الشحن للاختبار',
            'phone' => '01234567890',
            'address' => 'عنوان شركة الشحن للاختبار',
            'email' => 'test@shipping.com',
            'notes' => 'ملاحظات للاختبار',
        ];

        // إرسال طلب إنشاء شركة شحن جديدة
        $response = $this->post(route('shipping-companies.store'), $data);

        // التحقق من إعادة التوجيه بعد الحفظ
        $response->assertRedirect(route('shipping-companies.index'));
        
        // التحقق من وجود الشركة في قاعدة البيانات
        $this->assertDatabaseHas('shipping_companies', [
            'name' => 'شركة الشحن للاختبار',
            'email' => 'test@shipping.com',
        ]);
    }

    /**
     * اختبار عرض تفاصيل شركة شحن
     */
    public function test_show_shipping_company()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();

        // زيارة صفحة عرض تفاصيل شركة الشحن
        $response = $this->get(route('shipping-companies.show', $shippingCompany->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات شركة الشحن
        $response->assertViewHas('shippingCompany');
    }

    /**
     * اختبار تعديل شركة شحن
     */
    public function test_edit_shipping_company()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();

        // زيارة صفحة تعديل شركة الشحن
        $response = $this->get(route('shipping-companies.edit', $shippingCompany->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات شركة الشحن
        $response->assertViewHas('shippingCompany');
    }

    /**
     * اختبار تحديث شركة شحن
     */
    public function test_update_shipping_company()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();

        // بيانات التحديث
        $data = [
            'name' => 'شركة الشحن المحدثة',
            'phone' => '01234567890',
            'address' => 'عنوان شركة الشحن المحدث',
            'email' => 'updated@shipping.com',
            'notes' => 'ملاحظات محدثة',
        ];

        // إرسال طلب تحديث شركة الشحن
        $response = $this->put(route('shipping-companies.update', $shippingCompany->id), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('shipping-companies.index'));
        
        // التحقق من تحديث الشركة في قاعدة البيانات
        $this->assertDatabaseHas('shipping_companies', [
            'id' => $shippingCompany->id,
            'name' => 'شركة الشحن المحدثة',
            'email' => 'updated@shipping.com',
        ]);
    }

    /**
     * اختبار حذف شركة شحن
     */
    public function test_delete_shipping_company()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();

        // إرسال طلب حذف شركة الشحن
        $response = $this->delete(route('shipping-companies.destroy', $shippingCompany->id));

        // التحقق من إعادة التوجيه بعد الحذف
        $response->assertRedirect(route('shipping-companies.index'));
        
        // التحقق من حذف الشركة من قاعدة البيانات
        $this->assertDatabaseMissing('shipping_companies', [
            'id' => $shippingCompany->id,
        ]);
    }
}
