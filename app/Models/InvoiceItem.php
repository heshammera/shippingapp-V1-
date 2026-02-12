<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            // Calculate amount automatically
            $item->amount = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            // Recalculate invoice totals
            $item->invoice->recalculateFromItems();
        });

        static::deleted(function ($item) {
            // Recalculate invoice totals after deletion
            if ($item->invoice) {
                $item->invoice->recalculateFromItems();
            }
        });
    }

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
