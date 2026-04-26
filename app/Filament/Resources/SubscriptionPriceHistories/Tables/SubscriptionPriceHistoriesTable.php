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
                    ->label('Prix')
                    ->getStateUsing(fn($record) => number_format($record->price, 2, ',', ' ') . ' €/mois'),
                TextColumn::make('stripe_price_id')
                    ->label('Stripe Price ID')
                    ->copyable()
                    ->copyMessage('ID copié')
                    ->fontFamily('mono')
                    ->color('gray'),
                TextColumn::make('effective_from')
                    ->label('En vigueur depuis')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('creator.first_name')
                    ->label('Modifié par')
                    ->getStateUsing(fn($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                TextColumn::make('created_at')
                    ->label('Créé le')
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
