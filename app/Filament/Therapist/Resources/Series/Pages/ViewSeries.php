<?php

namespace App\Filament\Therapist\Resources\Series\Pages;

use App\Filament\Therapist\Resources\Series\SeriesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSeries extends ViewRecord
{
    protected static string $resource = SeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
