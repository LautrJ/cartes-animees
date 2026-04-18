<?php

namespace App\Filament\Resources\CommissionRateHistories\Pages;

use App\Filament\Resources\CommissionRateHistories\CommissionRateHistoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommissionRateHistory extends ViewRecord
{
    protected static string $resource = CommissionRateHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
