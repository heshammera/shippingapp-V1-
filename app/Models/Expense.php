<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'description',
        'amount',
        'expense_date',
        'expense_account_id',
        'paid_via_account_id',
        'status',
        'receipt_image',
        'user_id',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($expense) {
            if (empty($expense->reference_number)) {
                $expense->reference_number = self::generateReferenceNumber();
            }
            if (empty($expense->user_id)) {
                $expense->user_id = auth()->id() ?? 1;
            }
        });
    }

    private static function generateReferenceNumber()
    {
        $year = now()->year;
        $latest = self::whereYear('created_at', $year)->latest()->first();
        $sequence = $latest ? ((int) substr($latest->reference_number, -5)) + 1 : 1;
        return 'EXP-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function expenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }

    public function paidViaAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'paid_via_account_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Workflow Actions
    public function approve(User $user)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        // Trigger generic ledger posting via service (to be hooked or called in Action)
    }

    public function reject(User $user, string $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsPaid(ChartOfAccount $fromAccount)
    {
        $this->update([
            'status' => 'paid',
            'paid_via_account_id' => $fromAccount->id,
        ]);
    }
}
