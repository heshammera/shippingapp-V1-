<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة الجماعية
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * علاقة مع المستخدمين
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * علاقة مع الصلاحيات
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * التحقق من وجود صلاحية معينة للدور
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }
        
        return $this->permissions->contains($permission);
    }

    /**
     * إضافة صلاحية للدور
     */
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        
        $this->permissions()->syncWithoutDetaching($permission);
        
        return $this;
    }

    /**
     * إزالة صلاحية من الدور
     */
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        
        $this->permissions()->detach($permission);
        
        return $this;
    }
}
