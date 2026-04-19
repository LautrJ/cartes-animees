<?php

namespace App\Filament\Therapist\Resources\PaymentInfos\Pages;

use App\Filament\Therapist\Resources\PaymentInfos\PaymentInfoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentInfo extends ViewRecord
{
    protected static string $resource = PaymentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
