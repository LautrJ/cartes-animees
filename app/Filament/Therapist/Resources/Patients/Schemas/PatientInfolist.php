<?php

namespace App\Filament\Therapist\Resources\Patients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('Prénom'),
                        TextEntry::make('last_name')
                            ->label('Nom'),
                        TextEntry::make('birthdate')
                            ->label('Date de naissance')
                            ->date('d/m/Y')
                            ->placeholder('Non renseignée'),
                        TextEntry::make('parent.first_name')
                            ->label('Parent')
                            ->getStateUsing(fn($record) => "{$record->parent->first_name} {$record->parent->last_name}"),
                    ]),

                Section::make('Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('Aucune note')
                            ->columnSpanFull(),
                    ]),

                Section::make('Séries')
                    ->schema([
                        TextEntry::make('series_count')
                            ->label('Séries débloquées')
                            ->getStateUsing(fn($record) => $record->series()->count()),
                        TextEntry::make('completed_series')
                            ->label('Séries complétées')
                            ->getStateUsing(fn($record) => $record->completedSeries()->count()),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
