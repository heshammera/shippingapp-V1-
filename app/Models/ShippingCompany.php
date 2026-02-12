<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCompany extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة الجماعية
     */
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    /**
     * تحويل الحقول
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع الشحنات
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * علاقة مع المندوبين
     */
    public function deliveryAgents(): HasMany
    {
        return $this->hasMany(DeliveryAgent::class);
    }

    /**
     * علاقة مع التحصيلات
     */
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }
    
    public function getRouteKeyName()
{
    return 'id';
}
}
