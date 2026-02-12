<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($variant) {
            if (empty($variant->barcode)) {
                $variant->barcode = app(\App\Services\BarcodeService::class)->generateUniqueBarcode();
            }
        });
    }

    protected $fillable = [
        'product_id',
        'sku',
        'color',
        'size',
        'stock_quantity',
        'reserved_quantity',
        'low_stock_threshold',
        'is_unlimited',
        'barcode',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_unlimited' => 'boolean',
    ];

    // ========== Relationships ==========
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class, 'variant_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'variant_id');
    }

    // ========== Computed Properties ==========

    /**
     * Calculate available quantity (stock - reserved)
     */
    public function getAvailableQuantityAttribute(): int
    {
        if ($this->is_unlimited) {
            return PHP_INT_MAX; // Unlimited stock
        }
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    /**
     * Check if variant is low on stock
     */
    public function getIsLowStockAttribute(): bool
    {
        if ($this->is_unlimited) {
            return false;
        }
        return $this->available_quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if variant can fulfill a specific quantity
     */
    public function canFulfill(int $quantity): bool
    {
        if ($this->is_unlimited) {
            return true;
        }
        return $this->available_quantity >= $quantity;
    }

    // ========== Scopes ==========

    /**
     * Scope for low stock variants
     */
    public function scopeLowStock($query)
    {
        return $query->where('is_unlimited', false)
            ->whereRaw('(stock_quantity - reserved_quantity) <= low_stock_threshold');
    }

    /**
     * Scope for out of stock variants
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('is_unlimited', false)
            ->whereRaw('(stock_quantity - reserved_quantity) <= 0');
    }

    /**
     * Scope for available variants (in stock)
     */
    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('is_unlimited', true)
              ->orWhereRaw('(stock_quantity - reserved_quantity) > 0');
        });
    }

    // ========== Helper Methods ==========

    /**
     * Get full display name
     */
    public function getFullNameAttribute(): string
    {
        $parts = [$this->product->name];
        if ($this->color) $parts[] = $this->color;
        if ($this->size) $parts[] = $this->size;
        
        return implode(' - ', $parts);
    }

    /**
     * Get stock status as text
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->is_unlimited) {
            return 'غير محدود';
        }
        
        if ($this->available_quantity <= 0) {
            return 'نفد من المخزون';
        }
        
        if ($this->is_low_stock) {
            return 'مخزون منخفض';
        }
        
        return 'متوفر';
    }
}
