<?php

namespace App\Enums;

enum UserRole: string
{
    case Parent = 'parent';
    case Therapist = 'therapist';
    case Admin = 'admin';
}
