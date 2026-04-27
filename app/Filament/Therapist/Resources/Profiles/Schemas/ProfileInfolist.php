<?php

namespace App\Filament\Therapist\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')->label('Prénom'),
                        TextEntry::make('last_name')->label('Nom'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('phone')->label('Téléphone')->placeholder('Non renseigné'),
                    ]),

                Section::make('Code d\'invitation')
                    ->schema([
                        TextEntry::make('invitation_code')
                            ->label('Code d\'invitation')
                            ->copyable()
                            ->copyMessage('Code copié !')
                            ->fontFamily('mono'),
                    ]),
            ]);
    }
}
