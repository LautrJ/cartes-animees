<?php

namespace App\Filament\Resources\CommissionRateHistories\Pages;

use App\Filament\Resources\CommissionRateHistories\CommissionRateHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommissionRateHistories extends ListRecords
{
    protected static string $resource = CommissionRateHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
