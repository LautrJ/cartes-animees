<?php

namespace App\Filament\Resources\Cards\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardInfolist
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
                    ]),

                Section::make('Médias')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('gif_path')
                            ->label('GIF')
                            ->placeholder('Aucun fichier'),
                        TextEntry::make('video_path')
                            ->label('Vidéo')
                            ->placeholder('Aucun fichier'),
                        TextEntry::make('sound_path')
                            ->label('Son')
                            ->placeholder('Aucun fichier'),
                    ]),

                Section::make('Métadonnées')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('width')
                            ->label('Largeur')
                            ->suffix(' px')
                            ->placeholder('-'),
                        TextEntry::make('height')
                            ->label('Hauteur')
                            ->suffix(' px')
                            ->placeholder('-'),
                        TextEntry::make('duration')
                            ->label('Durée')
                            ->suffix(' sec')
                            ->placeholder('-'),
                    ]),

                Section::make('Statut')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_validated')
                            ->label('Validée')
                            ->boolean(),
                        TextEntry::make('series_count')
                            ->label('Utilisée dans')
                            ->getStateUsing(fn($record) => $record->series()->count() . ' série(s)'),
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
