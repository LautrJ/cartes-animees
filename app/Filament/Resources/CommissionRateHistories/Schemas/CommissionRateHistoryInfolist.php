<?php

namespace App\Filament\Resources\CommissionRateHistories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommissionRateHistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Taux de commission')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('rate')
                            ->label('Taux')
                            ->getStateUsing(fn ($record) => $record->rate.' €/patient'),
                        TextEntry::make('effective_from')
                            ->label('En vigueur depuis')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('creator.first_name')
                            ->label('Modifié par')
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
