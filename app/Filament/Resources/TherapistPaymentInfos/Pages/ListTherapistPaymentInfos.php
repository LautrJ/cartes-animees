<?php

namespace App\Filament\Resources\TherapistPaymentInfos\Pages;

use App\Filament\Resources\TherapistPaymentInfos\TherapistPaymentInfoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTherapistPaymentInfos extends ListRecords
{
    protected static string $resource = TherapistPaymentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
