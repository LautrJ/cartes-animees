<?php

namespace App\Filament\Resources\Series\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesForm
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
                        Textarea::make('description.fr')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('created_by')
                            ->label('Créé par')
                            ->options(
                                User::whereIn('role', [UserRole::Admin, UserRole::Therapist])
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => "{$u->first_name} {$u->last_name}"])
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Médias')
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->label('Miniature')
                            ->image()
                            ->nullable(),
                    ]),

                Section::make('Paramètres')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_base')
                            ->label('Série de base'),
                        Toggle::make('is_validated')
                            ->label('Validée'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
