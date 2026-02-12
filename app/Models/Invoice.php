<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'uuid',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'paid_at',
        'qr_code',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->uuid)) {
                $invoice->uuid = Str::uuid();
            }
            
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
            
            if (empty($invoice->issue_date)) {
                $invoice->issue_date = now();
            }
            
            if (empty($invoice->due_date)) {
                $invoice->due_date = now()->addDays(30);
            }
            
            if (empty($invoice->created_by)) {
                $invoice->created_by = auth()->id();
            }
            
            // Auto-calculate amounts
            self::calculateAmounts($invoice);
        });

        static::created(function ($invoice) {
            if ($invoice->status === 'issued') {
                app(\App\Services\AccountingService::class)->createInvoiceEntry($invoice);
            }
        });
        
        static::updating(function ($invoice) {
            self::calculateAmounts($invoice);
            
            // Post to Journal if status changed to 'issued'
            if ($invoice->isDirty('status') && $invoice->status === 'issued') {
                app(\App\Services\AccountingService::class)->createInvoiceEntry($invoice);
            }
        });
    }
    
    private static function calculateAmounts($invoice)
    {
        // Calculate tax amount
        $invoice->tax_amount = $invoice->subtotal * ($invoice->tax_rate / 100);
        
        // Calculate total
        $invoice->total_amount = $invoice->subtotal + $invoice->tax_amount - $invoice->discount_amount;
    }
    
    private static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        
        // Get last invoice number for this month
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['issued', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'issued')
            ->where('due_date', '<', now());
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'issued' && $this->due_date < now();
    }

    public function getAmountPaidAttribute()
    {
        return $this->status === 'paid' ? $this->total_amount : 0;
    }

    public function getAmountDueAttribute()
    {
        return $this->status !== 'paid' ? $this->total_amount : 0;
    }

    // Methods
    public function recalculateFromItems()
    {
        $this->subtotal = $this->items()->sum('amount');
        $this->save();
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function getQrCodeImageAttribute()
    {
        // Simple data for internal tracking
        $data = json_encode([
            'id' => $this->invoice_number,
            'date' => $this->issue_date->format('Y-m-d'),
            'total' => $this->total_amount,
            'tax' => $this->tax_amount,
        ]);
        
        return QrCode::size(150)->generate($data);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}
