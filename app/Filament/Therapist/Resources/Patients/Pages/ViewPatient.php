<?php

namespace App\Filament\Therapist\Resources\Patients\Pages;

use App\Filament\Therapist\Resources\Patients\Actions\EditNotesAction;
use App\Filament\Therapist\Resources\Patients\Actions\EndFollowUpAction;
use App\Filament\Therapist\Resources\Patients\Actions\UnlockSeriesAction;
use App\Filament\Therapist\Resources\Patients\PatientResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EndFollowUpAction::make(),
        ];
    }
}
