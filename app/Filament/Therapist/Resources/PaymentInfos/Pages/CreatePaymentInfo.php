<?php

namespace App\Filament\Therapist\Resources\PaymentInfos\Pages;

use App\Filament\Therapist\Resources\PaymentInfos\PaymentInfoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentInfo extends CreateRecord
{
    protected static string $resource = PaymentInfoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

}
