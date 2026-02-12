<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->barcode)) {
                $product->barcode = app(\App\Services\BarcodeService::class)->generateUniqueBarcode();
            }
        });
    }

    protected $fillable = [
        'name', 'price', 'colors', 'sizes', 'cost_price', 
        'supplier_id', 'reorder_point', 'barcode', 'track_inventory', 
        'sku', 'description', 'category_id', 'is_active'
    ];

    // ... (casts remain same)

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryLevels()
    {
        return $this->hasManyThrough(InventoryLevel::class, ProductVariant::class, 'product_id', 'variant_id');
    }

    protected $casts = [
        'colors' => 'array',
        'sizes' => 'array',
        'price' => 'float',
        'cost_price' => 'float',
    ];

    // ترجيع النصوص كما هي من قاعدة البيانات
    public function getColorsStringAttribute(): string
    {
        // لو في قيمة في الخاصية الحالية، استخدمها، وإلا هات الخام من القاعدة
        $val = $this->attributes['colors'] ?? $this->getRawOriginal('colors');
        return (string) $val;
    }

    public function getSizesStringAttribute(): string
    {
        $val = $this->attributes['sizes'] ?? $this->getRawOriginal('sizes');
        return (string) $val;
    }

    // مساعدات اختيارية لو حبيت تفك CSV لاحقًا
    public function availableColors(): array
    {
        $raw = $this->colors_string;
        if ($raw === '') return [];

        // Try JSON decode first
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback to CSV
        $parts = preg_split('/\s*[,\x{060C}]\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter(array_map('trim', $parts)));
    }

    public function availableSizes(): array
    {
        $raw = $this->sizes_string;
        if ($raw === '') return [];

        // Try JSON decode first
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $parts = preg_split('/\s*[,\x{060C}]\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter(array_map('trim', $parts)));
    }

    public function tierPrices()
    {
        return $this->hasMany(TierPrice::class);
    }

    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class);
    }

    public function variants()
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }

    // Accessor for total stock (Physical)
    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants()->sum('stock_quantity');
    }

    // Accessor for reserved stock
    public function getReservedStockAttribute(): int
    {
        return (int) $this->variants()->sum('reserved_quantity');
    }

    // Accessor for available stock
    public function getAvailableStockAttribute(): int
    {
        // We calculate sum of (stock - reserved) for each variant, ensuring no negative available
        // But since available accessor in variant handles max(0), we can just sum that logic?
        // Actually, easiest is stock - reserved if we assume data integrity.
        // Better to iterate to respect unlimited flag? 
        // If unlimited, sum is meaningless. Let's return -1 or handle presentation layer.
        // For now, let's stick to simple integers for normal products.
        // If any variant is unlimited, the product is effectively unlimited? 
        // Let's keep it simple: sum of stock - sum of reserved.
        return $this->total_stock - $this->reserved_stock;
    }
}
