<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TherapistPaymentInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'iban',
        'bic',
        'bank_name',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function getTherapistNameAttribute(): string
    {
        return "{$this->user->first_name} {$this->user->last_name}";
    }
}
