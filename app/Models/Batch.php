<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
        'production_date' => 'date',
    ];

    // Relationships
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Scopes
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereDate('expiry_date', '<=', now()->addDays($days))
                     ->whereDate('expiry_date', '>=', now());
    }
    
    public function scopeExpired($query)
    {
        return $query->whereDate('expiry_date', '<', now());
    }
}
