<?php

namespace App\Enums;

enum SettingType: string implements \Filament\Support\Contracts\HasLabel
{
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';

    public function getLabel(): ?string
    {
        return __('enums.setting_type.' . $this->value);
    }
}
