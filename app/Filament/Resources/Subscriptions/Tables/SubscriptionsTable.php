<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Enums\SubscriptionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('child.first_name')
                    ->label('Enfant')
                    ->getStateUsing(fn ($record) => "{$record->child->first_name} {$record->child->last_name}"),
                TextColumn::make('child.parent.first_name')
                    ->label('Parent')
                    ->getStateUsing(fn ($record) => "{$record->child->parent->first_name} {$record->child->parent->last_name}"),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (SubscriptionStatus $state) => match ($state) {
                        SubscriptionStatus::Active => 'success',
                        SubscriptionStatus::PastDue => 'danger',
                        SubscriptionStatus::Canceled => 'gray',
                        SubscriptionStatus::Free => 'info',
                    }),
                TextColumn::make('override_price')
                    ->label('Prix')
                    ->getStateUsing(fn ($record) => match (true) {
                        $record->override_price === null => 'Normal',
                        (float) $record->override_price === 0.0 => 'Gratuit',
                        default => number_format($record->override_price, 2).' €',
                    }),
                TextColumn::make('current_period_end')
                    ->label('Renouvellement')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'past_due' => 'En retard',
                        'canceled' => 'Annulé',
                        'free' => 'Gratuit',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
