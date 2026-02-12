<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentStatus extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة الجماعية
     */
    protected $fillable = [
        'name',
        'code',
        'sort_order',
        'color',
        'is_default',
    ];

    /**
     * تحويل الحقول
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * علاقة مع الشحنات
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'status_id');
    }
    
    public function status()
{
    return $this->belongsTo(ShipmentStatus::class, 'status_id');
}

}
