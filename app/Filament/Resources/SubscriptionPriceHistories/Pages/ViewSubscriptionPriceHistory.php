<?php

namespace App\Filament\Resources\SubscriptionPriceHistories\Pages;

use App\Filament\Resources\SubscriptionPriceHistories\SubscriptionPriceHistoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptionPriceHistory extends ViewRecord
{
    protected static string $resource = SubscriptionPriceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
