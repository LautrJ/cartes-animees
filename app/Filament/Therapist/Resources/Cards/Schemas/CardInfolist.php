<?php

namespace App\Filament\Therapist\Resources\Cards\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informations')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name.fr')
                            ->label('Nom'),
                        TextEntry::make('creator.first_name')
                            ->label('Créé par')
                            ->getStateUsing(fn($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('duration')
                            ->label('Durée')
                            ->suffix(' sec')
                            ->placeholder('-'),
                        TextEntry::make('series_count')
                            ->label('Utilisée dans')
                            ->getStateUsing(fn($record) => $record->series()->count() . ' série(s)'),
                    ]),

                Section::make('Médias')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('gif_path')
                            ->label('GIF')
                            ->placeholder('-'),
                        TextEntry::make('video_path')
                            ->label('Vidéo')
                            ->placeholder('-'),
                        TextEntry::make('sound_path')
                            ->label('Son')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
