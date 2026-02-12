<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total_cost = $item->quantity_ordered * $item->unit_cost;
        });

        static::saved(function ($item) {
            $item->purchaseOrder->update([
                'total_amount' => $item->purchaseOrder->items()->sum('total_cost')
            ]);
        });

        static::deleted(function ($item) {
            if ($item->purchaseOrder) {
                $item->purchaseOrder->update([
                    'total_amount' => $item->purchaseOrder->items()->sum('total_cost')
                ]);
            }
        });
    }
}
