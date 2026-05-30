<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts\Pages;

use App\Filament\Therapist\Resources\TherapistPayouts\TherapistPayoutResource;
use Filament\Resources\Pages\ListRecords;

class ListTherapistPayouts extends ListRecords
{
    protected static string $resource = TherapistPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
