<?php

namespace App\Enums;

enum ContentValidationStatus: string implements \Filament\Support\Contracts\HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return __('enums.content_validation_status.' . $this->value);
    }
}
