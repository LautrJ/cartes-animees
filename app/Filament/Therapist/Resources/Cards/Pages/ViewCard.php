<?php

namespace App\Filament\Therapist\Resources\Cards\Pages;

use App\Filament\Therapist\Resources\Cards\CardResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCard extends ViewRecord
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
