<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'first_name',
        'last_name',
        'birthdate',
        'avatar',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    // Relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function therapists(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'child_therapist', 'child_id', 'therapist_id')
            ->withPivot(['assigned_by', 'assigned_at', 'ended_at'])
            ->withTimestamps();
    }

    public function activeTherapists(): BelongsToMany
    {
        return $this->therapists()->wherePivotNull('ended_at');
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'child_series')
            ->withPivot(['unlocked_by', 'status', 'unlocked_at', 'completed_at'])
            ->withTimestamps()
            ->withCasts([
                'unlocked_at' => 'datetime',
                'completed_at' => 'datetime',
            ]);
    }

    // Helpers
    public function completedSeries(): BelongsToMany
    {
        return $this->series()->wherePivot('status', 'completed');
    }

    // Filament
    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('subscriptions', fn ($q) => $q->active());
    }

    public function scopeWithAccessibleSubscription($query)
    {
        return $query->whereHas('subscriptions', fn ($q) => $q->accesible());
    }
}
