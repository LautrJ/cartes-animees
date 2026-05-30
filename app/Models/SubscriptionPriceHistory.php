<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPriceHistory extends Model
{
    protected $fillable = [
        'price',
        'stripe_price_id',
        'effective_from',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'effective_from' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
