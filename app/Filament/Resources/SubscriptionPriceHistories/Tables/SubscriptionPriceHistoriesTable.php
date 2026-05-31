<?php

namespace App\Filament\Resources\SubscriptionPriceHistories\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionPriceHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('effective_from', 'desc')
            ->columns([
                TextColumn::make('price')
                    ->label(__('filament.subscription_price_histories.table.price'))
                    ->getStateUsing(fn ($record) => number_format($record->price, 2, ',', ' ').' €/mois'),
                TextColumn::make('stripe_price_id')
                    ->label(__('filament.subscription_price_histories.table.stripe_price_id'))
                    ->copyable()
                    ->copyMessage(__('filament.subscription_price_histories.table.copy_message'))
                    ->fontFamily('mono')
                    ->color('gray'),
                TextColumn::make('effective_from')
                    ->label(__('filament.subscription_price_histories.table.effective_from'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('creator.first_name')
                    ->label(__('filament.subscription_price_histories.table.creator'))
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                TextColumn::make('created_at')
                    ->label(__('filament.subscription_price_histories.table.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
