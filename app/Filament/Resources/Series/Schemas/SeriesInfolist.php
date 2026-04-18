<?php

namespace App\Filament\Resources\Series\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name.fr')
                            ->label('Nom'),
                        TextEntry::make('creator.first_name')
                            ->label('Créé par')
                            ->getStateUsing(fn($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('description.fr')
                            ->label('Description')
                            ->placeholder('Aucune description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Paramètres')
                    ->columns(3)
                    ->schema([
                        IconEntry::make('is_base')
                            ->label('Série de base')
                            ->boolean(),
                        IconEntry::make('is_validated')
                            ->label('Validée')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créée le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mise à jour le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
