<?php

namespace App\Enums;

enum UserRole: string implements \Filament\Support\Contracts\HasLabel
{
    case Parent = 'parent';
    case Therapist = 'therapist';
    case Admin = 'admin';

    public function getLabel(): ?string
    {
        return __('enums.user_role.' . $this->value);
    }
}
