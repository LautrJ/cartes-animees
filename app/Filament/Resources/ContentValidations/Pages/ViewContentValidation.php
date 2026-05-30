<?php

namespace App\Filament\Resources\ContentValidations\Pages;

use App\Filament\Resources\ContentValidations\Actions\ApproveContentValidation;
use App\Filament\Resources\ContentValidations\Actions\RejectContentValidation;
use App\Filament\Resources\ContentValidations\ContentValidationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContentValidation extends ViewRecord
{
    protected static string $resource = ContentValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApproveContentValidation::make(),
            RejectContentValidation::make(),
        ];
    }
}
