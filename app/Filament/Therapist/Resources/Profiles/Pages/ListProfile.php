<?php

namespace App\Filament\Therapist\Resources\Profiles\Pages;

use App\Filament\Therapist\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    public function mount(): void
    {
        redirect(ProfileResource::getUrl('view', ['record' => auth()->id()]));
    }
}
