<?php

namespace App\Filament\Therapist\Resources\ContentValidations\Pages;

use App\Filament\Therapist\Resources\ContentValidations\ContentValidationResource;
use Filament\Resources\Pages\ListRecords;

class ListContentValidations extends ListRecords
{
    protected static string $resource = ContentValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
