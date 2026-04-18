<?php

namespace App\Enums;

enum ContentValidationStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
