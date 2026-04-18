<?php

namespace App\Filament\Resources\Cards\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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
                        Select::make('created_by')
                            ->label('Créé par')
                            ->options(
                                User::whereIn('role', [UserRole::Admin, UserRole::Therapist])
                                    ->get()
                                    ->mapWithKeys(fn($u) => [$u->id => "{$u->first_name} {$u->last_name}"])
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
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

                Section::make('Statut')
                    ->schema([
                        Toggle::make('is_validated')
                            ->label('Validée')
                            ->default(false),
                    ]),
            ]);
    }
}
