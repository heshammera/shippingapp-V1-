<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Expense;
use App\Models\User;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار عرض قائمة المصاريف
     */
    public function test_index_expenses()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض المصاريف للاختبار
        Expense::factory()->count(3)->create([
            'created_by' => $user->id
        ]);

        // زيارة صفحة قائمة المصاريف
        $response = $this->get(route('expenses.index'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على المصاريف
        $response->assertViewHas('expenses');
    }

    /**
     * اختبار إنشاء مصروف جديد
     */
    public function test_create_expense()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // زيارة صفحة إنشاء مصروف جديد
        $response = $this->get(route('expenses.create'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
    }

    /**
     * اختبار حفظ مصروف جديد
     */
    public function test_store_expense()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // بيانات المصروف الجديد
        $data = [
            'title' => 'مصروف للاختبار',
            'amount' => 500,
            'expense_date' => '2025-04-26',
            'notes' => 'مصروف للاختبار',
        ];

        // إرسال طلب إنشاء مصروف جديد
        $response = $this->post(route('expenses.store'), $data);

        // التحقق من إعادة التوجيه بعد الحفظ
        $response->assertRedirect(route('expenses.index'));
        
        // التحقق من وجود المصروف في قاعدة البيانات
        $this->assertDatabaseHas('expenses', [
            'title' => 'مصروف للاختبار',
            'amount' => 500,
            'created_by' => $user->id,
        ]);
    }

    /**
     * اختبار عرض تفاصيل مصروف
     */
    public function test_show_expense()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مصروف للاختبار
        $expense = Expense::factory()->create([
            'created_by' => $user->id
        ]);

        // زيارة صفحة عرض تفاصيل المصروف
        $response = $this->get(route('expenses.show', $expense->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات المصروف
        $response->assertViewHas('expense');
    }

    /**
     * اختبار تعديل مصروف
     */
    public function test_edit_expense()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مصروف للاختبار
        $expense = Expense::factory()->create([
            'created_by' => $user->id
        ]);

        // زيارة صفحة تعديل المصروف
        $response = $this->get(route('expenses.edit', $expense->id));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على بيانات المصروف
        $response->assertViewHas('expense');
    }

    /**
     * اختبار تحديث مصروف
     */
    public function test_update_expense()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مصروف للاختبار
        $expense = Expense::factory()->create([
            'created_by' => $user->id,
            'amount' => 500
        ]);

        // بيانات التحديث
        $data = [
            'title' => 'مصروف محدث للاختبار',
            'amount' => 750,
            'expense_date' => '2025-04-27',
            'notes' => 'مصروف محدث للاختبار',
        ];

        // إرسال طلب تحديث المصروف
        $response = $this->put(route('expenses.update', $expense->id), $data);

        // التحقق من إعادة التوجيه بعد التحديث
        $response->assertRedirect(route('expenses.index'));
        
        // التحقق من تحديث المصروف في قاعدة البيانات
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'مصروف محدث للاختبار',
            'amount' => 750,
        ]);
    }

    /**
     * اختبار حذف مصروف
     */
    public function test_delete_expense()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء مصروف للاختبار
        $expense = Expense::factory()->create([
            'created_by' => $user->id
        ]);

        // إرسال طلب حذف المصروف
        $response = $this->delete(route('expenses.destroy', $expense->id));

        // التحقق من إعادة التوجيه بعد الحذف
        $response->assertRedirect(route('expenses.index'));
        
        // التحقق من حذف المصروف من قاعدة البيانات
        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    /**
     * اختبار عرض تقرير المصاريف
     */
    public function test_expenses_report()
    {
        // إنشاء مستخدم وتسجيل الدخول
        $user = User::factory()->create();
        $this->actingAs($user);

        // إنشاء بعض المصاريف للاختبار
        Expense::factory()->count(5)->create([
            'created_by' => $user->id
        ]);

        // زيارة صفحة تقرير المصاريف
        $response = $this->get(route('expenses.report'));

        // التحقق من أن الصفحة تعرض بنجاح
        $response->assertStatus(200);
        
        // التحقق من أن الصفحة تحتوي على المصاريف
        $response->assertViewHas('expenses');
    }
}
