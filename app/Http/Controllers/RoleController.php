<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as SpatiePermission;

class RoleController extends Controller
{
    /**
     * عرض قائمة الأدوار
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }



    /**
     * عرض نموذج إنشاء دور جديد
     */

public function create()
{
    $permissions = Permission::all()->groupBy(function ($perm) {
        return explode('.', $perm->name)[0]; // زي shipments, users
    });

    return view('roles.create', compact('permissions'));
}

    /**
     * تخزين دور جديد في قاعدة البيانات
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:roles',
        'description' => 'nullable|string',
        'permissions' => 'required|array|min:1',
        'permissions.*' => 'exists:permissions,id',
    ]);

    DB::beginTransaction();

    try {
        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'guard_name' => 'web', // ✅ مهم جدًا
        ]);

        $permissions = Permission::whereIn('id', $validated['permissions'])->get();
        $role->syncPermissions($permissions);

        DB::commit();

        return redirect()->route('roles.index')->with('success', '✅ تم إضافة الدور بنجاح');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', '❌ فشل الإضافة: ' . $e->getMessage())->withInput();
    }
}


    /**
     * عرض تفاصيل دور محدد
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $users = $role->users()->paginate(10);
        
        return view('roles.show', compact('role', 'users'));
    }

    /**
     * عرض نموذج تعديل دور محدد
     */
     

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->get()->groupBy('group');

        $rolePermissions = $role->permissions->pluck('id')->toArray();
                        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function ($perm) {
                return explode('.', $perm->name)[0];
});
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * تحديث بيانات دور محدد في قاعدة البيانات
     */
    public function update(Request $request, Role $role)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        'description' => 'nullable|string',
        'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    DB::beginTransaction();
    try {
        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // ✅ هنا بنجيب الـ Permissions كـ Collection من الـ IDs
        $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $validated['permissions'])->get();

        // ✅ ونعمل sync باستخدامهم
        $role->syncPermissions($permissions);

        DB::commit();
        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage())
            ->withInput();
    }
}


    /**
     * حذف دور محدد من قاعدة البيانات
     */
    public function destroy(Role $role)
    {
        // التحقق من عدم وجود مستخدمين مرتبطين بالدور
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين');
        }
        
        DB::beginTransaction();
        
        try {
            // حذف العلاقات مع الصلاحيات
            $role->permissions()->detach();
            
            // حذف الدور
            $role->delete();
            
            DB::commit();
            
            return redirect()->route('roles.index')
                ->with('success', 'تم حذف الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('roles.index')
                ->with('error', 'حدث خطأ أثناء حذف الدور: ' . $e->getMessage());
        }
    }

public function editPermissions(Role $role)
{
    $permissions = Permission::all();
    return view('roles.edit-permissions', compact('role', 'permissions'));
}

public function updatePermissions(Request $request, Role $role)
{
    $role->permissions()->sync($request->input('permissions', []));
return response()->json(['status' => 'success']);
}


}
