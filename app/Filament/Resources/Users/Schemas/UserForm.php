<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),
                        Select::make('role')
                            ->label('Rôle')
                            ->options(UserRole::class)
                            ->default(UserRole::Parent)
                            ->required(),
                    ]),

                Section::make('Sécurité')
                    ->schema([
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->label('Confirmation du mot de passe')
                            ->password()
                            ->dehydrated(false),
                    ]),

                Section::make('Statut')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Compte actif')
                            ->default(true),
                    ]),
            ]);
    }
}
