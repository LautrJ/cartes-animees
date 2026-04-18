<?php

namespace App\Filament\Resources\ContentValidations\Pages;

use App\Filament\Resources\ContentValidations\ContentValidationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContentValidation extends ViewRecord
{
    protected static string $resource = ContentValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
