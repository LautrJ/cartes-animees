<?php

namespace App\Filament\Therapist\Resources\PaymentInfos\Pages;

use App\Filament\Therapist\Resources\PaymentInfos\PaymentInfoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentInfos extends ListRecords
{
    protected static string $resource = PaymentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
