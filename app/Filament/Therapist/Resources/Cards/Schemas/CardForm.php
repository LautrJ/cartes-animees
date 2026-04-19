<?php

namespace App\Filament\Therapist\Resources\Cards\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextInput::make('name.fr')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Médias')
                    ->columns(3)
                    ->schema([
                        FileUpload::make('gif_path')
                            ->label('GIF')
                            ->acceptedFileTypes(['image/gif'])
                            ->required(),
                        FileUpload::make('video_path')
                            ->label('Vidéo')
                            ->acceptedFileTypes(['video/mp4'])
                            ->required(),
                        FileUpload::make('sound_path')
                            ->label('Son')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])
                            ->required(),
                    ]),
            ]);
    }
}
