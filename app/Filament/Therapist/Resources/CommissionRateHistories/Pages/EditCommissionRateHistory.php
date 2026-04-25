<?php

namespace App\Filament\Therapist\Resources\CommissionRateHistories\Pages;

use App\Filament\Therapist\Resources\CommissionRateHistories\CommissionRateHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommissionRateHistory extends EditRecord
{
    protected static string $resource = CommissionRateHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
