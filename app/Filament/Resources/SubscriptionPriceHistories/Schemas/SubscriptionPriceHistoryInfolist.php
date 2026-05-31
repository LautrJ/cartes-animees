<?php

namespace App\Filament\Resources\SubscriptionPriceHistories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionPriceHistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.subscription_price_histories.infolist.section_title'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('price')
                            ->label(__('filament.subscription_price_histories.infolist.price'))
                            ->getStateUsing(fn ($record) => number_format($record->price, 2, ',', ' ').' €/mois'),
                        TextEntry::make('stripe_price_id')
                            ->label(__('filament.subscription_price_histories.infolist.stripe_price_id'))
                            ->copyable()
                            ->copyMessage(__('filament.subscription_price_histories.infolist.copy_message'))
                            ->fontFamily('mono')
                            ->color('gray'),
                        TextEntry::make('effective_from')
                            ->label(__('filament.subscription_price_histories.infolist.effective_from'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('creator.first_name')
                            ->label(__('filament.subscription_price_histories.infolist.creator'))
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('created_at')
                            ->label(__('filament.subscription_price_histories.infolist.created_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
