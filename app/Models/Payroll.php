<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'month',
        'year',
        'total_amount',
        'status',
        'journal_entry_id',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($payroll) {
            if (empty($payroll->reference_number)) {
                $payroll->reference_number = 'PAY-' . $payroll->year . '-' . str_pad($payroll->month, 2, '0', STR_PAD_LEFT);
            }
            if (empty($payroll->created_by)) {
                $payroll->created_by = auth()->id() ?? 1;
            }
        });
    }

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
