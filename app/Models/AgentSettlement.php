<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'agent_id',
        'amount',
        'settlement_date',
        'receiving_account_id',
        'status',
        'journal_entry_id',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($settlement) {
            if (empty($settlement->reference_number)) {
                $settlement->reference_number = self::generateReference();
            }
            if (empty($settlement->user_id)) {
                $settlement->user_id = auth()->id() ?? 1;
            }
        });
    }

    private static function generateReference()
    {
        return 'SET-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }

    public function agent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'agent_id');
    }

    public function receivingAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'receiving_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
