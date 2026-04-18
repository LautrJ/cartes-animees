<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'child_id',
        'overridden_by',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'override_price',
        'current_period_start',
        'current_period_end',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'status'                => SubscriptionStatus::class,
            'override_price'        => 'decimal:2',
            'current_period_start'  => 'datetime',
            'current_period_end'    => 'datetime',
            'canceled_at'           => 'datetime',
        ];
    }

    // Relations
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function overriddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'overridden_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Filament
    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::Active);
    }
}
