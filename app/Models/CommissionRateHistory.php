<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'effective_from',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'rate'           => 'decimal:2',
            'effective_from' => 'datetime',
        ];
    }

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
