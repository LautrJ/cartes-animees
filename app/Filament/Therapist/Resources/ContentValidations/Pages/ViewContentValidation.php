<?php

namespace App\Filament\Therapist\Resources\ContentValidations\Pages;

use App\Filament\Therapist\Resources\ContentValidations\ContentValidationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContentValidation extends ViewRecord
{
    protected static string $resource = ContentValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
