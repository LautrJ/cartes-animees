<?php

namespace App\Filament\Therapist\Resources\Series\Pages;

use App\Enums\ContentValidationStatus;
use App\Filament\Therapist\Resources\Series\SeriesResource;
use App\Models\ContentValidation;
use App\Models\Series;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSeries extends CreateRecord
{
    protected static string $resource = SeriesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['is_validated'] = false;
        $data['is_active'] = false;
        $data['is_base'] = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        ContentValidation::create([
            'validatable_id' => $this->record->id,
            'validatable_type' => Series::class,
            'submitted_by' => auth()->id(),
            'status' => ContentValidationStatus::Pending,
            'submitted_at' => now(),
        ]);

        User::admins()->each(fn ($admin) => Notification::make()
            ->title(auth()->user()->getFilamentName().' a soumis une série en attente de validation.')
            ->body($this->record->name['fr'] ?? '')
            ->warning()
            ->sendToDatabase($admin)
        );
    }
}
