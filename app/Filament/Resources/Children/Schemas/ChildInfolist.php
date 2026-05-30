<?php

namespace App\Filament\Resources\Children\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
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
                            ->getStateUsing(fn ($record) => "{$record->parent->first_name} {$record->parent->last_name}"),
                    ]),

                Section::make('Orthophonistes')
                    ->schema([
                        TextEntry::make('activeTherapists')
                            ->label('Orthophonistes actifs')
                            ->getStateUsing(fn ($record) => $record->activeTherapists
                                ->map(fn ($t) => "{$t->first_name} {$t->last_name}")
                                ->join(', ') ?: '-'
                            ),
                    ]),

                Section::make('Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('Aucune note')
                            ->columnSpanFull(),
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
