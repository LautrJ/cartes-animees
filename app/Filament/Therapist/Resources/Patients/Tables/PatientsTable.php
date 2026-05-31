<?php

namespace App\Filament\Therapist\Resources\Patients\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('filament.therapist.patients.table.full_name'))
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(query: fn ($query, $search) => $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                    ),
                TextColumn::make('birthdate')
                    ->label(__('filament.therapist.patients.table.birthdate'))
                    ->date('d/m/Y')
                    ->placeholder('-'),
                TextColumn::make('parent.first_name')
                    ->label(__('filament.therapist.patients.table.parent'))
                    ->getStateUsing(fn ($record) => "{$record->parent->first_name} {$record->parent->last_name}"),
                TextColumn::make('series_count')
                    ->label(__('filament.therapist.patients.table.series_count'))
                    ->getStateUsing(fn ($record) => $record->series()->count()),
                TextColumn::make('created_at')
                    ->label(__('filament.therapist.patients.table.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
