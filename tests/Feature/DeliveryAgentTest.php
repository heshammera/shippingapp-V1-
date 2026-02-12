<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\DeliveryAgent;
use App\Models\User;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\ShippingCompany;

class DeliveryAgentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار عرض قائمة المندوبين
     */
    public function test_index_delivery_agents()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض المندوبين للاختبار
        DeliveryAgent::factory()->count(3)->create();

        // زيارة صفحة قائمة المندوبين
        $response = $this->get(route('delivery-agents.index'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على المندوبين
        $response->assertViewHas('deliveryAgents');
    }

    /**
     * اختبار إنشاء مندوب جديد
     */
    public function test_create_delivery_agent()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // زيارة صفحة إنشاء مندوب جديد
        $response = $this->get(route('delivery-agents.create'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار حفظ مندوب جديد
     */
    public function test_store_delivery_agent()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // بيانات المندوب الجديد
        $data = [
            'name' => 'مندوب الاختبار',
            'phone' => '01234567890',
            'address' => 'عنوان مندوب الاختبار',
            'email' => 'test@agent.com',
            'national_id' => '12345678901234',
            'commission_rate' => 5,
            'notes' => 'ملاحظات للاختبار',
        ];

        // إرسال طلب إنشاء مندوب جديد
        $response = $this->post(route('delivery-agents.store'), $data);

        // التحقق من إعادة التوجيه بعد الحفظ
        $response->assertRedirect(route('delivery-agents.index'));
        
        // التحقق من وجود المندوب في قاعدة البيانات
        $this->assertDatabaseHas('delivery_agents', [
            'name' => 'مندوب الاختبار',
            'email' => 'test@agent.com',
        ]);
    }

    /**
     * اختبار عرض تفاصيل مندوب
     */
    public function test_show_delivery_agent()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مندوب للاختبار
        $deliveryAgent = DeliveryAgent::factory()->create();

        // زيارة صفحة عرض تفاصيل المندوب
        $response = $this->get(route('delivery-agents.show', $deliveryAgent->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات المندوب
        $response->assertViewHas('deliveryAgent');
    }

    /**
     * اختبار تعديل مندوب
     */
    public function test_edit_delivery_agent()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مندوب للاختبار
        $deliveryAgent = DeliveryAgent::factory()->create();

        // زيارة صفحة تعديل المندوب
        $response = $this->get(route('delivery-agents.edit', $deliveryAgent->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات المندوب
        $response->assertViewHas('deliveryAgent');
    }

    /**
     * اختبار تحديث مندوب
     */
    public function test_update_delivery_agent()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مندوب للاختبار
        $deliveryAgent = DeliveryAgent::factory()->create();

        // بيانات التحديث
        $data = [
            'name' => 'مندوب محدث',
            'phone' => '01234567890',
            'address' => 'عنوان مندوب محدث',
            'email' => 'updated@agent.com',
            'national_id' => '12345678901234',
            'commission_rate' => 7,
            'notes' => 'ملاحظات محدثة',
        ];

        // إرسال طلب تحديث المندوب
        $response = $this->put(route('delivery-agents.update', $deliveryAgent->id), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('delivery-agents.index'));
        
        // التحقق من تحديث المندوب في قاعدة البيانات
        $this->assertDatabaseHas('delivery_agents', [
            'id' => $deliveryAgent->id,
            'name' => 'مندوب محدث',
            'email' => 'updated@agent.com',
        ]);
    }

    /**
     * اختبار عرض شحنات المندوب
     */
    public function test_show_delivery_agent_shipments()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مندوب للاختبار
        $deliveryAgent = DeliveryAgent::factory()->create();
        
        // إنشاء شحنات للمندوب
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        
        Shipment::factory()->count(3)->create([
            'delivery_agent_id' => $deliveryAgent->id,
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id
        ]);

        // زيارة صفحة عرض شحنات المندوب
        $response = $this->get(route('delivery-agents.shipments', $deliveryAgent->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على المندوب والشحنات
        $response->assertViewHas('deliveryAgent');
        $response->assertViewHas('shipments');
    }

    /**
     * اختبار حذف مندوب
     */
    public function test_delete_delivery_agent()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مندوب للاختبار
        $deliveryAgent = DeliveryAgent::factory()->create();

        // إرسال طلب حذف المندوب
        $response = $this->delete(route('delivery-agents.destroy', $deliveryAgent->id));

        // التحقق من إعادة التوجيه بعد الحذف
        $response->assertRedirect(route('delivery-agents.index'));
        
        // التحقق من حذف المندوب من قاعدة البيانات
        $this->assertDatabaseMissing('delivery_agents', [
            'id' => $deliveryAgent->id,
        ]);
    }
}
