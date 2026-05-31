<?php

namespace App\Filament\Therapist\Resources\CommissionRateHistories\Schemas;

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
                Section::make(__('filament.therapist.commission_rate_histories.infolist.section_commission_rate'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('rate')
                            ->label(__('filament.therapist.commission_rate_histories.infolist.rate'))
                            ->getStateUsing(fn ($record) => $record->rate.__('filament.therapist.commission_rate_histories.infolist.rate_suffix')),
                        TextEntry::make('effective_from')
                            ->label(__('filament.therapist.commission_rate_histories.infolist.effective_from'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('creator.first_name')
                            ->label(__('filament.therapist.commission_rate_histories.infolist.modified_by'))
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('created_at')
                            ->label(__('filament.therapist.commission_rate_histories.infolist.created_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
