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
            'status'       => InvoiceStatus::class,
            'amount'       => 'decimal:2',
            'period_start' => 'datetime',
            'period_end'   => 'datetime',
            'paid_at'      => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::Paid);
    }
}
