<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\ShippingCompany;



class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index()
    {
        $users = User::with('role')->get();
        return view('users.index', compact('users'));
    }

    /**
     * عرض نموذج إنشاء مستخدم جديد
     */
public function create()
{
    $roles = Role::all();
    $shippingCompanies = ShippingCompany::all(); // جلب جميع شركات الشحن
    return view('users.create', compact('roles', 'shippingCompanies'));
}


    /**
     * تخزين مستخدم جديد في قاعدة البيانات
     */
     
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|string|in:admin,moderator,viewer,delivery_agent,accountant,shipping_agent',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'expires_days' => 'nullable|integer|min:1',
        'expires_lifetime' => 'nullable',
    ]);

    // ✅ هنا نحدد قيمة expires_at بناءً على checkbox أو days
    $expiresAt = null;
    if ($request->has('expires_lifetime')) {
        $expiresAt = now()->addYears(100); // مدى الحياة = 100 سنة
    } elseif (!empty($validated['expires_days'])) {
        $expiresAt = now()->addDays($validated['expires_days']);
    }

    DB::beginTransaction();

    try {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $validated['role'],
            'role_id' => Role::where('name', $validated['role'])->value('id'),
                    'shipping_company_id' => $request->input('shipping_company_id'), // ← هنا تحفظها مباشرة
            'is_active' => $request->has('is_active'),
            'expires_at' => $expiresAt, // ← هذا السطر هو الذي يحفظ القيمة
        ]);

        DB::commit();
        return redirect()->route('users.index')->with('success', '✅ تم إضافة المستخدم بنجاح');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', '❌ فشل الإضافة: ' . $e->getMessage())->withInput();
    }
}

// public function store(Request $request)
//{
//    $validated = $request->validate([
//        'name' => 'required|string|max:255',
//        'email' => 'required|email|unique:users',
//        'password' => 'required|string|min:6|confirmed',
//        'role' => 'required', 'string', 'in:admin,accountant,moderator,delivery_agent,viewer,shipping_agent',
//        'phone' => 'nullable|string',
//        'address' => 'nullable|string',
//        'is_active' => 'nullable|boolean',
//        'expires_days' => 'nullable|integer|min:1',
//        'expires_lifetime' => 'nullable',
//    ]);
//
//    $expiresAt = null;
//    if (!$request->has('expires_lifetime') && !empty($validated['expires_days'])) {
//        $expiresAt = now()->addDays($validated['expires_days']);
//    }
//
//    DB::beginTransaction();
//
//    try {
//        $user = User::create([
//            'name' => $validated['name'],
//            'email' => $validated['email'], // ✅ تصحيح هنا
//            'password' => Hash::make($validated['password']), // ✅ تصحيح هنا
//            'phone' => $validated['phone'] ?? null,
//            'address' => $validated['address'] ?? null,
//            'role' => $validated['role'], // ✅ تخزين الدور
//            'role_id' => Role::where('name', $request->role)->value('id'), // ← ID الخاص بالدور
//            'is_active' => $request->has('is_active'),
//            'expires_at' => $expiresAt, // ← استخدم المتغير المحسوب هنا مش ثابت
//        ]);
//
//        DB::commit();
//        return redirect()->route('users.index')->with('success', '✅ تم إضافة المستخدم بنجاح');
//    } catch (\Exception $e) {
//        DB::rollBack();
//        return redirect()->back()->with('error', '❌ فشل الإضافة: ' . $e->getMessage())->withInput();
//    }
//}


    /**
     * عرض تفاصيل مستخدم محدد
     */
public function show(User $user)
{
    $expires_days = null;

    if ($user->expires_at && $user->expires_at->gt(now())) {
        $expires_days = now()->diffInDays($user->expires_at);
    }

    return view('users.show', compact('user', 'expires_days'));
}

    /**
     * عرض نموذج تعديل مستخدم محدد
     */
public function edit(User $user)
{
    $roles = Role::all();
    $shippingCompanies = ShippingCompany::all();

    // جلب مندوب الشحن المرتبط بالمستخدم (لو موجود)
    $deliveryAgent = $user->deliveryAgent ?? null;

    $expires_days = null;
    if ($user->expires_at && $user->expires_at->gt(now())) {
        $expires_days = now()->diffInDays($user->expires_at);
    }

    return view('users.edit', compact('user', 'roles', 'shippingCompanies', 'deliveryAgent', 'expires_days'));
}



    /**
     * تحديث بيانات مستخدم محدد في قاعدة البيانات
     */
    /**
     * تحديث بيانات مستخدم محدد في قاعدة البيانات
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|confirmed|min:6',
            'role' => 'required|string', // سنعتمد على الاسم
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'expires_days' => 'nullable|integer|min:1',
            'expires_lifetime' => 'nullable',
            'shipping_company_id' => 'nullable|exists:shipping_companies,id',
        ]);

        // تحضير البيانات للتحديث
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->has('is_active'),
            'shipping_company_id' => $validated['shipping_company_id'] ?? null,
        ];

        // تحديث كلمة المرور فقط إذا تم إدخالها
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        // حساب تاريخ الانتهاء
        if ($request->has('expires_lifetime')) {
            $data['expires_at'] = now()->addYears(100);
        } elseif (!empty($validated['expires_days'])) {
            $data['expires_at'] = now()->addDays($validated['expires_days']);
        }
        // إذا لم يتم تحديد أي خيار جديد، نحتفظ بالتاريخ القديم أو نجعله null حسب المنطق المطلوب.
        // هنا سنفترض أن المستخدم لو لم يدخل شيء لا نغير التاريخ إلا لو أراد ذلك صراحة (يمكن تعديل هذا المنطق).
        // لكن الكود السابق كان يعيد حسابه. لتسهيل الأمر: لو Lifetime checked -> 100 سنة. لو days filled -> days. غير ذلك لا نلمس expires_at إلا لو أردنا تصفيره؟
        // الكود السابق كان يتركه، لذا سنبقيه كما هو أو نحدثه فقط عند الإدخال.
        
        // الأفضل:
        if ($request->has('expires_lifetime') || $request->filled('expires_days')) {
             // تم تحديد قيمة جديدة
        } else {
             // لم يتم تحديد تغيير في الصلاحية، هل نحتفظ بالقديم؟ نعم.
             unset($data['expires_at']); 
        }

        DB::beginTransaction();

        try {
            // 1. تحديث بيانات المستخدم الأساسية
            $user->update($data);

            // 2. تحديث الدور باستخدام Spatie
            // نتأكد أن الدور موجود
            if ($request->filled('role')) {
                // حفظ اسم الدور في العمود القديم role للدعم العكسي إذا كان مستخدماً
                $user->role = $validated['role'];
                $user->saveQuietly();

                // تعيين الدور فعلياً باستخدام Spatie
                $user->syncRoles([$validated['role']]);
            }

            DB::commit();
            return redirect()->route('users.index')->with('success', '✅ تم تحديث بيانات المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ فشل التحديث: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * حذف مستخدم محدد من قاعدة البيانات
     */
    public function destroy(User $user)
    {
        // منع حذف المستخدم الحالي
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'لا يمكن حذف المستخدم الحالي');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }



    /**
     * تنفيذ إجراء جماعي على المستخدمين المحددين
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,extend',
            'selected_users' => 'required|array',
            'selected_users.*' => 'exists:users,id',
            'extension_days' => 'nullable|integer|min:1',
        ]);

        $query = User::whereIn('id', $validated['selected_users']);

        // منع التأثير على المستخدم الحالي
        if (in_array(auth()->id(), $validated['selected_users'])) {
            return redirect()->back()->with('error', '⚠️ لا يمكن تنفيذ إجراء جماعي يشمل حسابك الحالي.');
        }

        switch ($validated['action']) {
            case 'activate':
                $count = $query->update(['is_active' => true]);
                $message = "✅ تم تفعيل {$count} مستخدم بنجاح.";
                break;

            case 'deactivate':
                $count = $query->update(['is_active' => false]);
                $message = "⛔ تم تعطيل {$count} مستخدم بنجاح.";
                break;

            case 'delete':
                $count = $query->count();
                $query->delete();
                $message = "🗑️ تم حذف {$count} مستخدم بنجاح.";
                break;

            case 'extend':
                $days = $validated['extension_days'];
                if (!$days) {
                    return redirect()->back()->with('error', '⚠️ يجب تحديد عدد الأيام للتمديد.');
                }
                
                // نمر على المستخدمين واحداً تلو الآخر لتحديث التاريخ بشكل صحيح (إضافة للأيام المتبقية أو من الآن)
                $count = 0;
                foreach ($query->get() as $user) {
                    if (!$user->expires_at) continue; // تخطي المستخدمين "مدى الحياة"

                    $newExpiry = $user->expires_at->lt(now()) 
                        ? now()->addDays($days) // لو منتهي، نضيف من النهاردة
                        : $user->expires_at->addDays($days); // لو لسه شغال، نزود عليه
                    
                    $user->update(['expires_at' => $newExpiry, 'is_active' => true]);
                    $count++;
                }
                $message = "📅 تم تمديد صلاحية {$count} مستخدم لمدة {$days} يوم.";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

     public function updateThemeColor(Request $request) {
        $user = auth()->user();
        $request->validate([
            'color' => ['required', 'regex:/^#([0-9a-fA-F]{6})$/']
        ]);
        $user->theme_color = $request->color;
        $user->save();
        return response()->json(['message' => 'تم تحديث اللون بنجاح']);
    }
}
