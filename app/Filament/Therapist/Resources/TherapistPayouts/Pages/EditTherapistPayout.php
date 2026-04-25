<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts\Pages;

use App\Filament\Therapist\Resources\TherapistPayouts\TherapistPayoutResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTherapistPayout extends EditRecord
{
    protected static string $resource = TherapistPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
