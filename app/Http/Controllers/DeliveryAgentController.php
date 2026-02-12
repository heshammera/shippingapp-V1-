<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAgent;
use App\Models\ShippingCompany;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ShipmentStatus;



class DeliveryAgentController extends Controller
{
    /**
     * عرض قائمة المندوبين
     */
     

 public function index()
    {
            $shippingCompanies = ShippingCompany::all(); // تجيب كل شركات الشحن
$deliveredId = ShipmentStatus::where('name', 'تم التوصيل')->value('id');
$returnedId  = ShipmentStatus::where('name', 'مرتجع')->value('id');
$holdId      = ShipmentStatus::where('name', 'عهدة أو تدوير')->value('id');

$deliveryAgents = DeliveryAgent::with('shippingCompany')
    ->withCount([
        'shipments as delivered_count' => fn($q) => $q->where('status_id', $deliveredId),
        'shipments as returned_count'  => fn($q) => $q->where('status_id', $returnedId),
        'shipments as hold_count'      => fn($q) => $q->where('status_id', $holdId),
    ])
    ->paginate(20);
    

        return view('delivery_agents.index', compact('deliveryAgents', 'shippingCompanies'));
    }

    /**
     * عرض نموذج إنشاء مندوب جديد
     */
     
     
     
    public function create()
    {
        $shippingCompanies = ShippingCompany::all();
        return view('delivery_agents.create', compact('shippingCompanies'));
    }

    /**
     * تخزين مندوب جديد في قاعدة البيانات
     */
     
     
     
     
public function store(Request $request)
{
    // تحقق من صحة البيانات
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'national_id' => 'nullable|string|max:20',
        'shipping_company_id' => 'required|exists:shipping_companies,id',
        'max_edit_count' => 'required|integer|min:1',
        'is_active' => 'boolean',
        'notes' => 'nullable|string',
        'create_user' => 'boolean',
        'user_email' => 'required_if:create_user,1|email|max:255|unique:users,email',
        'user_password' => 'required_if:create_user,1|string|min:8',
    ]);

    $userId = null;

    // إنشاء حساب مستخدم فقط إذا كان الخيار مفعلاً
    if ($request->create_user) {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['user_email'],
            'password' => Hash::make($validated['user_password']),
            'role' => 'delivery_agent',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $userId = $user->id;
    }

    $deliveryAgent = DeliveryAgent::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'address' => $validated['address'] ?? null,
        'national_id' => $validated['national_id'] ?? null,
        'shipping_company_id' => $validated['shipping_company_id'],
        'user_id' => $userId,
        'max_edit_count' => $validated['max_edit_count'],
        'is_active' => $request->has('is_active'),
        'notes' => $validated['notes'] ?? null,
    ]);

    return redirect()->route('delivery-agents.index')->with('success', 'تم إضافة المندوب بنجاح');
}





    /**
     * عرض تفاصيل المندوب
     */
    public function show(DeliveryAgent $deliveryAgent)
    {
        $deliveryAgent->load(['shippingCompany', 'user', 'shipments']);
        
$deliveredId = \App\Models\ShipmentStatus::where('name', 'تم التوصيل')->value('id');
$returnedId  = \App\Models\ShipmentStatus::where('name', 'مرتجع')->value('id');
$pendingId   = \App\Models\ShipmentStatus::where('name', 'قيد الانتظار')->value('id'); // أو الاسم المناسب
$holdId      = ShipmentStatus::where('name', 'عهدة أو تدوير')->value('id'); // ← دي الجديدة

$shipmentStats = [
    'total'     => $deliveryAgent->shipments()->count(),
    'delivered' => $deliveryAgent->shipments()->where('status_id', $deliveredId)->count(),
    'returned'  => $deliveryAgent->shipments()->where('status_id', $returnedId)->count(),
    'pending'   => $deliveryAgent->shipments()->where('status_id', $pendingId)->count(),
    'hold'      => $deliveryAgent->shipments()->where('status_id', $holdId)->count(), // ← دي كمان جديدة

];

        return view('delivery_agents.show', compact('deliveryAgent', 'shipmentStats'));
    }

    /**
     * عرض نموذج تعديل المندوب
     */
public function edit(DeliveryAgent $deliveryAgent)
{
    $shippingCompanies = ShippingCompany::all();

    // التحقق إذا كان للمندوب مستخدم مرتبط
    if ($deliveryAgent->user_id) {
        $user = $deliveryAgent->user;
    } else {
        $user = null;
    }

    return view('delivery_agents.edit', compact('deliveryAgent', 'shippingCompanies', 'user'));
}


    /**
     * تحديث بيانات المندوب في قاعدة البيانات
     */
  public function update(Request $request, DeliveryAgent $deliveryAgent)
{

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'national_id' => 'nullable|string|max:20',
        'shipping_company_id' => 'required|exists:shipping_companies,id',
        'max_edit_count' => 'required|integer|min:1',
        'is_active' => 'boolean',
        'notes' => 'nullable|string',
        'reset_password' => 'boolean',
        'user_password' => 'required_if:reset_password,1|string|min:8',
    ]);

    // التحديث
    $deliveryAgent->update([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'address' => $validated['address'] ?? null,
        'national_id' => $validated['national_id'] ?? null,
        'shipping_company_id' => $validated['shipping_company_id'],
        'max_edit_count' => $validated['max_edit_count'],
        'is_active' => $validated['is_active'] ?? true,
        'notes' => $validated['notes'] ?? null,
    ]);

    return redirect()->route('delivery-agents.index')->with('success', 'تم تحديث المندوب بنجاح');
}



    /**
     * حذف المندوب من قاعدة البيانات
     */
    public function destroy(DeliveryAgent $deliveryAgent)
    {
        // حذف المستخدم المرتبط بالمندوب إذا وجد
        if ($deliveryAgent->user_id) {
            $user = User::find($deliveryAgent->user_id);
            if ($user) {
                $user->delete();
            }
        }

        $deliveryAgent->delete();

        return redirect()->route('delivery-agents.index')
            ->with('success', 'تم حذف المندوب بنجاح');
    }
    
    /**
     * عرض شحنات المندوب
     */






public function shipments(DeliveryAgent $agent)
{
    // تأكد إن العلاقة shipments معرفه في الموديل
    $shipments = $agent->shipments()->latest()->paginate(10);
    $statuses = ShipmentStatus::orderBy('sort_order')->get(); // أو orderBy('id')

    return view('delivery_agents.shipments', compact('agent', 'shipments', 'statuses'));
}
}