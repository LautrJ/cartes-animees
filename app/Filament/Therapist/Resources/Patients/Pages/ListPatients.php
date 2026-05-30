<?php

namespace App\Filament\Therapist\Resources\Patients\Pages;

use App\Filament\Therapist\Resources\Patients\PatientResource;
use Filament\Resources\Pages\ListRecords;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
