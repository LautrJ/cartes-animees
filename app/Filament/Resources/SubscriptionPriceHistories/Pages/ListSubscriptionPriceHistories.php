<?php

namespace App\Filament\Resources\SubscriptionPriceHistories\Pages;

use App\Filament\Resources\SubscriptionPriceHistories\SubscriptionPriceHistoryResource;
use App\Filament\Resources\SubscriptionPriceHistories\Widgets\SubscriptionPriceChart;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionPriceHistories extends ListRecords
{
    protected static string $resource = SubscriptionPriceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SubscriptionPriceChart::class,
        ];
    }
}
