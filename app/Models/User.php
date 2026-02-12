<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;





use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles, LogsActivity;

    
    
protected $primaryKey = 'id';
public $incrementing = true;
protected $keyType = 'int';


    /**
     * الحقول القابلة للتعبئة الجماعية
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active',
        'expires_at',
        'shipping_company_id',
        'last_login_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'phone'])
            ->useLogName('user')
            ->logOnlyDirty();
    }

public function username()
{
    return 'name';
}

// العلاقات
public function shippingCompany()
{
    return $this->belongsTo(\App\Models\ShippingCompany::class);
}

public function deliveryAgent()
{
    return $this->hasOne(\App\Models\DeliveryAgent::class, 'user_id');
}


    /**
     * الحقول المخفية
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل الحقول
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'expires_at' => 'datetime',

    ];



// Accessors مفيدة
public function getDaysRemainingAttribute()
{
    if (!$this->expires_at) {
        return null; // مدى الحياة
    }
    
    return now()->diffInDays($this->expires_at, false);
}

public function getIsExpiredAttribute()
{
    if (!$this->expires_at) {
        return false;
    }
    
    return $this->expires_at->isPast();
}

public function getExpiryStatusAttribute()
{
    if (!$this->expires_at) {
        return ['text' => 'مدى الحياة', 'color' => 'success'];
    }
    
    $days = $this->days_remaining;
    
    if ($days < 0) {
        return ['text' => 'منتهية', 'color' => 'danger'];
    } elseif ($days < 3) {
        return ['text' => "$days يوم", 'color' => 'danger'];
    } elseif ($days < 7) {
        return ['text' => "$days أيام", 'color' => 'warning'];
    } else {
        return ['text' => "$days يوم", 'color' => 'primary'];
    }
}


public function canAccessPanel(Panel $panel): bool
{
    // السماح للجميع  - الصلاحيات ستتحكم في كل Resource
    return $this->is_active && !$this->is_expired;
}
    
}
