<?php

namespace App\Filament\Resources\Cards\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name.fr')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('duration')
                            ->label('Durée (secondes)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(30)
                            ->helperText('Durée d\'affichage de chaque animation avant de passer à la suivante'),
                    ]),

                Section::make('Médias')
                    ->columns(3)
                    ->schema([
                        FileUpload::make('drawn_animation_path')
                            ->label('Animation dessinée')
                            ->disk('cards')
                            ->directory('drawn')
                            ->acceptedFileTypes(['image/gif', 'video/mp4'])
                            ->required(),
                        FileUpload::make('real_animation_path')
                            ->label('Animation réelle')
                            ->disk('cards')
                            ->directory('real')
                            ->acceptedFileTypes(['image/gif', 'video/mp4'])
                            ->required(),
                        FileUpload::make('sound_path')
                            ->label('Son')
                            ->disk('cards')
                            ->directory('sounds')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])
                            ->required(),
                    ]),

                Section::make('Statut')
                    ->schema([
                        Toggle::make('is_validated')
                            ->label('Validée')
                            ->default(true),
                    ]),
            ]);
    }
}
