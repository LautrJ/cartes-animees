<?php

namespace App\Filament\Therapist\Widgets;

use App\Filament\Therapist\Resources\Patients\PatientResource;
use App\Models\Child;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TherapistPatientsWidget extends BaseWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.therapist.widgets.therapist_patients.heading');
    }

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Child::query()
                    ->whereHas('therapists', fn ($q) => $q
                        ->where('users.id', auth()->id())
                        ->whereNull('child_therapist.ended_at')
                    )
                    ->where(fn ($q) => $q
                        ->whereDoesntHave('series')
                        ->orWhereHas('series', fn ($q) => $q
                            ->where('child_series.unlocked_at', '<', now()->subMonth())
                        )
                    )
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('filament.therapist.widgets.therapist_patients.patient'))
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}"),
                TextColumn::make('series_count')
                    ->label(__('filament.therapist.widgets.therapist_patients.series_count'))
                    ->getStateUsing(fn ($record) => $record->series()->count()),
                TextColumn::make('last_unlock')
                    ->label(__('filament.therapist.widgets.therapist_patients.last_unlock'))
                    ->getStateUsing(function ($record) {
                        $lastSeries = $record->series()
                            ->orderByPivot('unlocked_at', 'desc')
                            ->first();

                        return $lastSeries?->pivot->unlocked_at
                            ? Carbon::parse($lastSeries->pivot->unlocked_at)->format('d/m/Y')
                            : __('filament.therapist.widgets.therapist_patients.never');
                    }),
            ])
            ->recordUrl(fn ($record) => PatientResource::getUrl('view', ['record' => $record]))
            ->toolbarActions([]);
    }
}
