<?php

namespace App\Filament\Therapist\Resources\Profiles\Pages;

use App\Filament\Therapist\Resources\Profiles\ProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
