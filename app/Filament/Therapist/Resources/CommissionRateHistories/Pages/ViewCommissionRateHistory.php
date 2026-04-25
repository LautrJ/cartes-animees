<?php

namespace App\Filament\Therapist\Resources\CommissionRateHistories\Pages;

use App\Filament\Therapist\Resources\CommissionRateHistories\CommissionRateHistoryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCommissionRateHistory extends ViewRecord
{
    protected static string $resource = CommissionRateHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
