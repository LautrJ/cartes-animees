<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'stripe_invoice_id',
        'amount',
        'status',
        'invoice_pdf',
        'period_start',
        'period_end',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'amount' => 'decimal:2',
            'period_start' => 'datetime',
            'period_end' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    // Relations
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // Filament
    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', InvoiceStatus::Draft);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', InvoiceStatus::Open);
    }

    public function scopeUncollectible($query)
    {
        return $query->where('status', InvoiceStatus::Uncollectible);
    }

    public function scopeVoid($query)
    {
        return $query->where('status', InvoiceStatus::Void);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }
}
