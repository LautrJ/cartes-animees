<?php

namespace App\Filament\Therapist\Resources\CommissionRateHistories\Pages;

use App\Filament\Therapist\Resources\CommissionRateHistories\Widgets\CommissionRateChart;
use App\Filament\Therapist\Resources\CommissionRateHistories\CommissionRateHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListCommissionRateHistories extends ListRecords
{
    protected static string $resource = CommissionRateHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CommissionRateChart::class,
        ];
    }
}
