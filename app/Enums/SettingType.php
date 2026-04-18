<?php

namespace App\Enums;

enum SettingType: string
{
    case String  = 'string';
    case Integer = 'integer';
    case Float   = 'float';
    case Boolean = 'boolean';
}
