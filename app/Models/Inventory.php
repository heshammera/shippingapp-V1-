<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // ← جديد
class Inventory extends Model
{
    
        use HasFactory, SoftDeletes; // ← جديد

    protected $fillable = ['product_id','color','size','quantity','low_stock_alert','is_unlimited'];

    protected $casts = [
        'quantity' => 'integer',
        'is_unlimited' => 'boolean',
    ];

    public function isUnlimited(): bool
    {
        return (bool) $this->is_unlimited;
    }

    public function isLow(): bool
    {
        // لو غير محدود عمره ما يكون قليل
        if ($this->isUnlimited()) return false;
        // لو عندك عتبة إنذار، غيّرها هنا
        return $this->quantity <= 5;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

