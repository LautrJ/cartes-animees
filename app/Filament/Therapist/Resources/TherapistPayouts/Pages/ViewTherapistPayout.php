<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts\Pages;

use App\Filament\Therapist\Resources\TherapistPayouts\TherapistPayoutResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTherapistPayout extends ViewRecord
{
    protected static string $resource = TherapistPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
