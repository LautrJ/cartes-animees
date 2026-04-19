<?php

namespace App\Filament\Therapist\Resources\Cards\Pages;

use App\Enums\ContentValidationStatus;
use App\Filament\Therapist\Resources\Cards\CardResource;
use App\Models\ContentValidation;
use Filament\Resources\Pages\CreateRecord;

class CreateCard extends CreateRecord
{
    protected static string $resource = CardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by']   = auth()->id();
        $data['is_validated'] = false;
        return $data;
    }

    protected function afterCreate(): void
    {
        ContentValidation::create([
            'validatable_id'   => $this->record->id,
            'validatable_type' => \App\Models\Card::class,
            'submitted_by'     => auth()->id(),
            'status'           => ContentValidationStatus::Pending,
            'submitted_at'     => now(),
        ]);
    }
}
