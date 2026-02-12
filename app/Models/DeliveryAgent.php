<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryAgent extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    /**
     * الحقول القابلة للتعبئة الجماعية
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name', 'phone', 'email', 'address', 'national_id', 'shipping_company_id', 'max_edit_count', 'is_active', 'notes', 'user_id', 'account_id'
];



    /**
     * الحقول التي يجب تحويلها إلى أنواع محددة
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'max_edit_count' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    /**
     * علاقة المندوب بالمستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة المندوب بشركة الشحن
     */
    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    /**
     * علاقة المندوب بالشحنات
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
