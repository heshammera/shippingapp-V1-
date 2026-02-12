<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false; // We only use created_at

    protected $fillable = [
        'warehouse_id',
        'batch_id',
        'variant_id',
        'shipment_id',
        'movement_type',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reason',
        'reference_number',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'created_at' => 'datetime',
    ];

    // ========== Relationships ==========

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== Scopes ==========

    /**
     * Scope for specific movement type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope for movements within date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for movements by specific variant
     */
    public function scopeForVariant($query, int $variantId)
    {
        return $query->where('variant_id', $variantId);
    }

    // ========== Helper Methods ==========

    /**
     * Get movement type in Arabic
     */
    public function getTypeInArabicAttribute(): string
    {
        return match($this->movement_type) {
            'purchase' => 'شراء',
            'adjustment' => 'تعديل',
            'reserve' => 'حجز',
            'release' => 'فك حجز',
            'deduct' => 'خصم',
            'return' => 'إرجاع',
            'transfer' => 'نقل',
            default => $this->movement_type,
        };
    }

    /**
     * Get formatted quantity change with sign
     */
    public function getFormattedQuantityChangeAttribute(): string
    {
        $sign = $this->quantity_change >= 0 ? '+' : '';
        return $sign . $this->quantity_change;
    }
}
