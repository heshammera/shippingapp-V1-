<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'type',
        'reference_type',
        'reference_id',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($entry) {
            if (empty($entry->entry_number)) {
                $entry->entry_number = self::generateEntryNumber();
            }
            if (empty($entry->created_by)) {
                $entry->created_by = auth()->id() ?? 1; // Default to admin if no auth
            }
        });
    }

    private static function generateEntryNumber()
    {
        $year = now()->year;
        $latest = self::whereYear('created_at', $year)->latest()->first();
        $sequence = $latest ? ((int) substr($latest->entry_number, -6)) + 1 : 1;
        return 'JE-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function reference()
    {
        return $this->morphTo();
    }

    // Logic
    public function isBalanced(): bool
    {
        $totalDebit = $this->lines->sum('debit');
        $totalCredit = $this->lines->sum('credit');
        
        // Use a small epsilon for float comparison safety
        return abs($totalDebit - $totalCredit) < 0.01;
    }

    public function post()
    {
        if (!$this->isBalanced()) {
            throw new \Exception('Journal Entry is not balanced.');
        }

        $this->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);
        
        // Here we could update account balances if we were tracking them in a separate table
        // But typically we calculate balances on the fly from journal lines
    }
}
