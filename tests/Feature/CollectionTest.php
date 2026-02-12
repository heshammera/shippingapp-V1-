<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Collection;
use App\Models\ShippingCompany;
use App\Models\User;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار عرض قائمة التحصيلات
     */
    public function test_index_collections()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        
        // إنشاء بعض التحصيلات للاختبار
        Collection::factory()->count(3)->create([
            'shipping_company_id' => $shippingCompany->id,
            'created_by' => $user->id
        ]);

        // زيارة صفحة قائمة التحصيلات
        $response = $this->get(route('collections.index'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على التحصيلات
        $response->assertViewHas('collections');
    }

    /**
     * اختبار إنشاء تحصيل جديد
     */
    public function test_create_collection()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // زيارة صفحة إنشاء تحصيل جديد
        $response = $this->get(route('collections.create'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار حفظ تحصيل جديد
     */
    public function test_store_collection()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();

        // بيانات التحصيل الجديد
        $data = [
            'shipping_company_id' => $shippingCompany->id,
            'amount' => 1000,
            'collection_date' => '2025-04-26',
            'notes' => 'تحصيل للاختبار',
        ];

        // إرسال طلب إنشاء تحصيل جديد
        $response = $this->post(route('collections.store'), $data);

        // التحقق من إعادة التوجيه بعد الحفظ
        $response->assertRedirect(route('collections.index'));
        
        // التحقق من وجود التحصيل في قاعدة البيانات
        $this->assertDatabaseHas('collections', [
            'shipping_company_id' => $shippingCompany->id,
            'amount' => 1000,
            'created_by' => $user->id,
        ]);
    }

    /**
     * اختبار عرض تفاصيل تحصيل
     */
    public function test_show_collection()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        
        // إنشاء تحصيل للاختبار
        $collection = Collection::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'created_by' => $user->id
        ]);

        // زيارة صفحة عرض تفاصيل التحصيل
        $response = $this->get(route('collections.show', $collection->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات التحصيل
        $response->assertViewHas('collection');
    }

    /**
     * اختبار تعديل تحصيل
     */
    public function test_edit_collection()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        
        // إنشاء تحصيل للاختبار
        $collection = Collection::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'created_by' => $user->id
        ]);

        // زيارة صفحة تعديل التحصيل
        $response = $this->get(route('collections.edit', $collection->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات التحصيل
        $response->assertViewHas('collection');
    }

    /**
     * اختبار تحديث تحصيل
     */
    public function test_update_collection()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $anotherCompany = ShippingCompany::factory()->create();
        
        // إنشاء تحصيل للاختبار
        $collection = Collection::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'created_by' => $user->id,
            'amount' => 1000
        ]);

        // بيانات التحديث
        $data = [
            'shipping_company_id' => $anotherCompany->id,
            'amount' => 1500,
            'collection_date' => '2025-04-27',
            'notes' => 'تحصيل محدث للاختبار',
        ];

        // إرسال طلب تحديث التحصيل
        $response = $this->put(route('collections.update', $collection->id), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('collections.index'));
        
        // التحقق من تحديث التحصيل في قاعدة البيانات
        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'shipping_company_id' => $anotherCompany->id,
            'amount' => 1500,
        ]);
    }

    /**
     * اختبار حذف تحصيل
     */
    public function test_delete_collection()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        
        // إنشاء تحصيل للاختبار
        $collection = Collection::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'created_by' => $user->id
        ]);

        // إرسال طلب حذف التحصيل
        $response = $this->delete(route('collections.destroy', $collection->id));

        // التحقق من إعادة التوجيه بعد الحذف
        $response->assertRedirect(route('collections.index'));
        
        // التحقق من حذف التحصيل من قاعدة البيانات
        $this->assertDatabaseMissing('collections', [
            'id' => $collection->id,
        ]);
    }

    /**
     * اختبار عرض تقرير التحصيلات
     */
    public function test_collections_report()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        
        // إنشاء بعض التحصيلات للاختبار
        Collection::factory()->count(5)->create([
            'shipping_company_id' => $shippingCompany->id,
            'created_by' => $user->id
        ]);

        // زيارة صفحة تقرير التحصيلات
        $response = $this->get(route('collections.report'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على التحصيلات
        $response->assertViewHas('collections');
    }
}
