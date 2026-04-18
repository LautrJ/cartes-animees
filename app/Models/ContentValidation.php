<?php

namespace App\Models;

use App\Enums\ContentValidationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentValidation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'validatable_id',
        'validatable_type',
        'submitted_by',
        'reviewed_by',
        'status',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => ContentValidationStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at'  => 'datetime',
        ];
    }

    // Relations
    public function validatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Filament
    public function scopePending($query)  { return $query->where('status', ContentValidationStatus::Pending); }
    public function scopeApproved($query) { return $query->where('status', ContentValidationStatus::Approved); }
    public function scopeRejected($query) { return $query->where('status', ContentValidationStatus::Rejected); }
}
