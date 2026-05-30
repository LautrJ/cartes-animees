<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
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
                        TextEntry::make('email')
                            ->label('Email')
                            ->columnSpanFull(),
                        TextEntry::make('phone')
                            ->label('Téléphone')
                            ->default('-'),
                        TextEntry::make('role')
                            ->label('Rôle')
                            ->badge()
                            ->color(fn (UserRole $state) => match ($state) {
                                UserRole::Admin => 'danger',
                                UserRole::Therapist => 'warning',
                                UserRole::Parent => 'success',
                            }),
                    ]),

                Section::make('Statut')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->label('Compte actif')
                            ->boolean(),
                        TextEntry::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Non vérifié'),
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
