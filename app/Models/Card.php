<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'gif_path',
        'video_path',
        'sound_path',
        'width',
        'height',
        'duration',
        'is_validated',
    ];

    protected function casts(): array
    {
        return [
            'name'         => 'array',
            'is_validated' => 'boolean',
        ];
    }

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'series_cards')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function validations(): MorphMany
    {
        return $this->morphMany(ContentValidation::class, 'validatable');
    }

    // Filament
    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopeUnvalidated($query)
    {
        return $query->where('is_validated', false);
    }

    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }
}
