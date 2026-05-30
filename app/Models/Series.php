<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Series extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'description',
        'thumbnail_path',
        'is_base',
        'is_validated',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'is_base' => 'boolean',
            'is_validated' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'series_cards')
            ->withPivot('order')
            ->orderByPivot('order')
            ->withTimestamps();
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_series')
            ->withPivot(['unlocked_by', 'status', 'unlocked_at', 'completed_at'])
            ->withTimestamps()
            ->withCasts([
                'unlocked_at' => 'datetime',
                'completed_at' => 'datetime',
            ]);
    }

    public function validations(): MorphMany
    {
        return $this->morphMany(ContentValidation::class, 'validatable');
    }

    // Filament
    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopeUnvalidated($query)
    {
        return $query->where('is_validated', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);

    }

    public function scopeUnactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }
}
