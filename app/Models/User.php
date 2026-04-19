<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;


class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'stripe_customer_id',
        'invitation_code',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'role'              => UserRole::class,
            'is_active'         => 'boolean',
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relations
    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'parent_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'created_by');
    }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class, 'created_by');
    }

    public function paymentInfo(): HasOne
    {
        return $this->hasOne(TherapistPaymentInfo::class);
    }

    public function therapistPayouts(): HasMany
    {
        return $this->hasMany(TherapistPayout::class, 'therapist_id');
    }

    public function processedPayouts(): HasMany
    {
        return $this->hasMany(TherapistPayout::class, 'processed_by');
    }

    public function patientsAsTherapist(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_therapist', 'therapist_id', 'child_id')
            ->withPivot(['assigned_by', 'assigned_at', 'ended_at'])
            ->withTimestamps();
    }

    public function activePatients(): BelongsToMany
    {
        return $this->patientsAsTherapist()->wherePivotNull('ended_at');
    }

    // Helpers
    public function isAdmin(): bool     { return $this->role === UserRole::Admin; }
    public function isTherapist(): bool { return $this->role === UserRole::Therapist; }
    public function isParent(): bool    { return $this->role === UserRole::Parent; }

    // Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return match($panel->getId()) {
            'admin' => $this->isAdmin(),
            'therapist' => $this->isTherapist() || $this->isAdmin(),
            default => false,
        };
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function canImpersonate(): bool
    {
        return $this->isAdmin();
    }

    public function canBeImpersonated(): bool
    {
        return !$this->isAdmin();
    }
}
