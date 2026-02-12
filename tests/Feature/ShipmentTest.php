<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\ShippingCompany;
use App\Models\DeliveryAgent;
use App\Models\User;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار عرض قائمة الشحنات
     */
    public function test_index_shipments()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض الشحنات للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        Shipment::factory()->count(5)->create([
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id
        ]);

        // زيارة صفحة قائمة الشحنات
        $response = $this->get(route('shipments.index'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على الشحنات
        $response->assertViewHas('shipments');
    }

    /**
     * اختبار إنشاء شحنة جديدة
     */
    public function test_create_shipment()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // زيارة صفحة إنشاء شحنة جديدة
        $response = $this->get(route('shipments.create'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار حفظ شحنة جديدة
     */
    public function test_store_shipment()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شركة شحن وحالة شحنة للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        $deliveryAgent = DeliveryAgent::factory()->create();

        // بيانات الشحنة الجديدة
        $data = [
            'tracking_number' => 'TRACK123456',
            'customer_name' => 'عميل الاختبار',
            'customer_phone' => '01234567890',
            'customer_address' => 'عنوان عميل الاختبار',
            'product_name' => 'منتج الاختبار',
            'quantity' => 2,
            'cost_price' => 100,
            'selling_price' => 150,
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id,
            'delivery_agent_id' => $deliveryAgent->id,
            'shipping_date' => '2025-04-26',
            'notes' => 'ملاحظات للاختبار',
        ];

        // إرسال طلب إنشاء شحنة جديدة
        $response = $this->post(route('shipments.store'), $data);

        // التحقق من إعادة التوجيه بعد الحفظ
        $response->assertRedirect(route('shipments.index'));
        
        // التحقق من وجود الشحنة في قاعدة البيانات
        $this->assertDatabaseHas('shipments', [
            'tracking_number' => 'TRACK123456',
            'customer_name' => 'عميل الاختبار',
            'product_name' => 'منتج الاختبار',
        ]);
    }

    /**
     * اختبار عرض تفاصيل شحنة
     */
    public function test_show_shipment()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شحنة للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        $shipment = Shipment::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id
        ]);

        // زيارة صفحة عرض تفاصيل الشحنة
        $response = $this->get(route('shipments.show', $shipment->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات الشحنة
        $response->assertViewHas('shipment');
    }

    /**
     * اختبار تعديل شحنة
     */
    public function test_edit_shipment()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شحنة للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        $shipment = Shipment::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id
        ]);

        // زيارة صفحة تعديل الشحنة
        $response = $this->get(route('shipments.edit', $shipment->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات الشحنة
        $response->assertViewHas('shipment');
    }

    /**
     * اختبار تحديث شحنة
     */
    public function test_update_shipment()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شحنة للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        $deliveryAgent = DeliveryAgent::factory()->create();
        $shipment = Shipment::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id
        ]);

        // بيانات التحديث
        $data = [
            'tracking_number' => 'UPDATED123',
            'customer_name' => 'عميل محدث',
            'customer_phone' => '01234567890',
            'customer_address' => 'عنوان عميل محدث',
            'product_name' => 'منتج محدث',
            'quantity' => 3,
            'cost_price' => 120,
            'selling_price' => 180,
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id,
            'delivery_agent_id' => $deliveryAgent->id,
            'shipping_date' => '2025-04-27',
            'notes' => 'ملاحظات محدثة',
        ];

        // إرسال طلب تحديث الشحنة
        $response = $this->put(route('shipments.update', $shipment->id), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('shipments.index'));
        
        // التحقق من تحديث الشحنة في قاعدة البيانات
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'tracking_number' => 'UPDATED123',
            'customer_name' => 'عميل محدث',
            'product_name' => 'منتج محدث',
        ]);
    }

    /**
     * اختبار تحديث حالة الشحنة
     */
    public function test_update_shipment_status()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شحنة للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $oldStatus = ShipmentStatus::factory()->create(['name' => 'قيد الانتظار']);
        $newStatus = ShipmentStatus::factory()->create(['name' => 'تم التسليم']);
        
        $shipment = Shipment::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $oldStatus->id
        ]);

        // بيانات تحديث الحالة
        $data = [
            'status_id' => $newStatus->id,
            'notes' => 'تم تحديث الحالة للاختبار',
        ];

        // إرسال طلب تحديث حالة الشحنة
        $response = $this->put(route('shipments.update-status', $shipment->id), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect();
        
        // التحقق من تحديث حالة الشحنة في قاعدة البيانات
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status_id' => $newStatus->id,
        ]);
    }

    /**
     * اختبار حذف شحنة
     */
    public function test_delete_shipment()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء شحنة للاختبار
        $shippingCompany = ShippingCompany::factory()->create();
        $status = ShipmentStatus::factory()->create();
        $shipment = Shipment::factory()->create([
            'shipping_company_id' => $shippingCompany->id,
            'status_id' => $status->id
        ]);

        // إرسال طلب حذف الشحنة
        $response = $this->delete(route('shipments.destroy', $shipment->id));

        // التحقق من إعادة التوجيه بعد الحذف
        $response->assertRedirect(route('shipments.index'));
        
        // التحقق من حذف الشحنة من قاعدة البيانات
        $this->assertDatabaseMissing('shipments', [
            'id' => $shipment->id,
        ]);
    }
}
