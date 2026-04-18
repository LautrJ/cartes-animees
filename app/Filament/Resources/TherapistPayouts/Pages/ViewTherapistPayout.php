<?php

namespace App\Filament\Resources\TherapistPayouts\Pages;

use App\Filament\Resources\TherapistPayouts\TherapistPayoutResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTherapistPayout extends ViewRecord
{
    protected static string $resource = TherapistPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
