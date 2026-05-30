<?php

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    // Helpers
    public function getTypedValue(): mixed
    {
        return match ($this->type) {
            SettingType::Integer => (int) $this->value,
            SettingType::Float => (float) $this->value,
            SettingType::Boolean => (bool) $this->value,
            default => $this->value
        };
    }

    // Filament
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }
}
