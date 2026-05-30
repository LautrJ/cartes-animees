<?php

namespace App\Filament\Resources\TherapistPaymentInfos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TherapistPaymentInfoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Orthophoniste')
                    ->schema([
                        TextEntry::make('user.first_name')
                            ->label('Nom')
                            ->getStateUsing(fn ($record) => "{$record->user->first_name} {$record->user->last_name}"),
                        TextEntry::make('user.email')
                            ->label('Email'),
                    ]),

                Section::make('Informations bancaires')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bank_name')
                            ->label('Banque'),
                        TextEntry::make('bic')
                            ->label('BIC'),
                        TextEntry::make('iban')
                            ->label('IBAN')
                            ->getStateUsing(fn ($record) => '•••• •••• •••• '.substr($record->iban, -4))
                            ->columnSpanFull(),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Ajouté le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
