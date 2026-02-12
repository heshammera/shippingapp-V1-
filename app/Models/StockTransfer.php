<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'notes',
        'reference_number',
        'transfer_date',
        'user_id',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($transfer) {
            if (empty($transfer->reference_number)) {
                $transfer->reference_number = 'TRF-' . date('Ymd') . '-' . rand(1000, 9999);
            }
            if (empty($transfer->user_id)) {
                $transfer->user_id = auth()->id();
            }
            if (empty($transfer->transfer_date)) {
                $transfer->transfer_date = now();
            }
        });
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
