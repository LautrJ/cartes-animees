<?php

namespace App\Enums;

enum ChildSeriesStatus: string implements \Filament\Support\Contracts\HasLabel
{
    case Unlocked = 'unlocked';
    case Completed = 'completed';

    public function getLabel(): ?string
    {
        return __('enums.child_series_status.' . $this->value);
    }
}
