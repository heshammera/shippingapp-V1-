<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    /**
     * الحقول القابلة للتعبئة الجماعية
     */
    protected $fillable = [
        'tracking_number',
        'barcode', // Added barcode
        'customer_name',
        'customer_phone',
        'alternate_phone',
        'customer_address',

        // لو لسه بتستخدم الحقول دي للعرض فقط ماشي؛ لكن لو بتعتمد على pivot/products بلاش تعتمد عليها في الحساب
        'product_id',
        'product_name',
        'color',
        'size',
        'quantity',
        'cost_price',
        'selling_price',
        'shipping_price',
        'governorate',
        'total_amount',
        'shipping_company',
        'status_id',
        'shipping_company_id',
        'delivery_agent_id',
        'shipping_date',
        'delivery_date',
        'return_date',
        'notes',
        'agent_notes',
        'product_description',

        // أعمدة التوثيق الجديدة (v2)
        'delivered_at',
        'returned_at',
        'inventory_reserved_at',
        'inventory_released_at',
        'inventory_returned_at',
        'is_printed',
        'print_date',
        'proof_photo',
        'signature',
        'latitude',
        'longitude',
    ];

    /**
     * تحويل الحقول
     */
    protected $casts = [
        'shipping_date' => 'date:Y-m-d',
        'delivery_date' => 'date:Y-m-d',
        'return_date'   => 'date:Y-m-d',
        'delivered_at'  => 'datetime',
        'returned_at'   => 'datetime',
        'inventory_reserved_at'  => 'datetime',
        'inventory_released_at'  => 'datetime',
        'inventory_returned_at'  => 'datetime',
        'shipping_company_id' => 'integer',
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity'      => 'integer',
        'edit_count'    => 'integer',
        'is_printed'    => 'boolean',
        'print_date'    => 'datetime',
    ];

    /**
     * علاقة مع شركة الشحن (بدون فلتر is_active هنا)
     * علشان الأوبزرفر يقدر يقرأ الشركة حتى لو اتغيّرت حالتها.
     */
    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class, 'shipping_company_id');
    }

    /**
     * لو حابب نسخة "نشطة فقط"؛ استخدمها في الـ UI بدل الأساسية
     */
    public function activeShippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class, 'shipping_company_id')
            ->where('is_active', true);
    }

    /**
     * سكوب الفلاتر
     */
    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['shipping_company_id'])) {
            $query->where('shipping_company_id', $filters['shipping_company_id']);
        }

        if (!empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }

        if (!empty($filters['delivery_agent_id'])) {
            $query->where('delivery_agent_id', $filters['delivery_agent_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('shipping_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('shipping_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * علاقة مع المندوب
     */
    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgent::class);
    }

    /**
     * علاقة المنتجات عبر Pivot
     * ملاحظة: pivot عندك فيه 'quantity' و 'price' (مش 'qty')
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'shipment_product')
            ->using(ShipmentProduct::class)
            ->withPivot(['color', 'size', 'quantity', 'price'])
            ->withTimestamps();
    }

    /**
     * علاقة مع حالة الشحنة
     * (Laravel تلقائيًا هيفترض foreign key = status_id وده اللي عندك)
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ShipmentStatus::class);
    }

    /**
     * حساب الربح (بسيط)
     */
    public function getProfit()
    {
        return $this->selling_price - $this->cost_price;
    }

    /**
     * علاقة قديمة مباشرة بالمنتج (لو لسه بتستخدمها)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
