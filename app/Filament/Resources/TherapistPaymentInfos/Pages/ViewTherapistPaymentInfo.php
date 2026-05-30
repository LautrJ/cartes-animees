<?php

namespace App\Filament\Resources\TherapistPaymentInfos\Pages;

use App\Filament\Resources\TherapistPaymentInfos\TherapistPaymentInfoResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTherapistPaymentInfo extends ViewRecord
{
    protected static string $resource = TherapistPaymentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
