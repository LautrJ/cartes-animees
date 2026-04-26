<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TherapistPayout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'therapist_id',
        'processed_by',
        'amount',
        'patient_count',
        'period_start',
        'period_end',
        'note',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'          => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'period_start'    => 'date',
            'period_end'      => 'date',
            'paid_at'         => 'datetime',
        ];
    }

    // Relations
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Filament
    public function scopePending($query)
    {
        return $query->whereNull('paid_at');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_at');
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }

    // Helpers
    public function getPayoutLabelAttribute(): string
    {
        return "#{$this->id} — {$this->therapist->first_name} {$this->therapist->last_name} ({$this->period_start->format('m/Y')})";
    }
}
