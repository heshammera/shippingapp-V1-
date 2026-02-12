<?php

namespace App\Http\Controllers;

use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    /**
     * عرض قائمة بجميع شركات الشحن
     */
    public function index()
    {
        $companies = ShippingCompany::withCount('shipments')->get();
    return view('shipping_companies.index', compact('companies'));
    }

    /**
     * عرض نموذج إنشاء شركة شحن جديدة
     */
    public function create()
    {
        return view('shipping_companies.create');
    }

    /**
     * تخزين شركة شحن جديدة في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $company = ShippingCompany::create($validated);

        return redirect()->route('shipping-companies.index')
            ->with('success', 'تم إضافة شركة الشحن بنجاح');
    }

    /**
     * عرض تفاصيل شركة شحن محددة مع الشحنات الخاصة بها
     */
    public function show(ShippingCompany $shippingCompany)
    {
        $shipments = $shippingCompany->shipments()->latest()->paginate(15);
        return view('shipping_companies.show', compact('shippingCompany', 'shipments'));
    }

    /**
     * عرض نموذج تعديل شركة شحن محددة
     */
    public function edit(ShippingCompany $shippingCompany)
    {
        return view('shipping_companies.edit', compact('shippingCompany'));
    }

    /**
     * تحديث بيانات شركة شحن محددة في قاعدة البيانات
     */
public function update(Request $request, ShippingCompany $shippingCompany)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        // لا تعتمد فقط على هذا السطر ↓
        'is_active' => 'sometimes|boolean',
    ]);

    // هنا نضمن أنه حتى لو ما تبعتش من الفورم يتم ضبطه بالقيمة الصحيحة
    $validated['is_active'] = $request->has('is_active');

    $shippingCompany->update($validated);

    return redirect()->route('shipping-companies.index')
        ->with('success', 'تم تحديث بيانات شركة الشحن بنجاح');
}


    /**
     * حذف شركة شحن محددة من قاعدة البيانات
     */
public function destroy(ShippingCompany $shippingCompany)
{
    // لو فيها شحنات متعلقة مش هنحذفها
    if ($shippingCompany->shipments()->count() > 0) {
        return redirect()->route('shipping-companies.index')
            ->with('error', 'لا يمكن حذف شركة الشحن لأنها تحتوي على شحنات مرتبطة بها');
    }

    // منع حذف شركات الشحن ID = 6 أو 7
    if (in_array($shippingCompany->id, [6, 7])) {
        return redirect()->back()->with('error', 'لا يمكن حذف شركة الشحن.');
    }

    $shippingCompany->delete();

    return redirect()->route('shipping-companies.index')
        ->with('success', 'تم حذف شركة الشحن بنجاح');
}


}

