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
                Section::make(__('filament.commission_rate_histories.infolist.sections.commission_rate'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('rate')
                            ->label(__('filament.commission_rate_histories.infolist.fields.rate'))
                            ->getStateUsing(fn ($record) => $record->rate.__('filament.commission_rate_histories.rate_suffix')),
                        TextEntry::make('effective_from')
                            ->label(__('filament.commission_rate_histories.infolist.fields.effective_from'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('creator.first_name')
                            ->label(__('filament.commission_rate_histories.infolist.fields.modified_by'))
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('created_at')
                            ->label(__('filament.commission_rate_histories.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
