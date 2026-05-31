<?php

namespace App\Enums;

enum SubscriptionStatus: string implements \Filament\Support\Contracts\HasLabel
{
    case Active = 'active';
    case PastDue = 'past_due';
    case Canceled = 'canceled';
    case Free = 'free';

    public function getLabel(): ?string
    {
        return __('enums.subscription_status.' . $this->value);
    }
}
