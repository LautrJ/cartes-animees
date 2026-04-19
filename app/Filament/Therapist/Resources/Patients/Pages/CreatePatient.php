<?php

namespace App\Filament\Therapist\Resources\Patients\Pages;

use App\Filament\Therapist\Resources\Patients\PatientResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
}
