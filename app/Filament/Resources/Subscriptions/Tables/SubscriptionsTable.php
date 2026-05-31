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
                    ->label(__('filament.subscriptions.table.columns.child'))
                    ->getStateUsing(fn ($record) => "{$record->child->first_name} {$record->child->last_name}"),
                TextColumn::make('child.parent.first_name')
                    ->label(__('filament.subscriptions.table.columns.parent'))
                    ->getStateUsing(fn ($record) => "{$record->child->parent->first_name} {$record->child->parent->last_name}"),
                TextColumn::make('status')
                    ->label(__('filament.subscriptions.table.columns.status'))
                    ->badge()
                    ->color(fn (SubscriptionStatus $state) => match ($state) {
                        SubscriptionStatus::Active => 'success',
                        SubscriptionStatus::PastDue => 'danger',
                        SubscriptionStatus::Canceled => 'gray',
                        SubscriptionStatus::Free => 'info',
                    }),
                TextColumn::make('override_price')
                    ->label(__('filament.subscriptions.table.columns.price'))
                    ->getStateUsing(fn ($record) => match (true) {
                        $record->override_price === null => __('filament.subscriptions.table.columns.price_normal'),
                        (float) $record->override_price === 0.0 => __('filament.subscriptions.table.columns.price_free'),
                        default => number_format($record->override_price, 2).' €',
                    }),
                TextColumn::make('current_period_end')
                    ->label(__('filament.subscriptions.table.columns.current_period_end'))
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament.subscriptions.table.columns.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.subscriptions.table.filters.status'))
                    ->options([
                        'active' => __('filament.subscriptions.table.filters.status_active'),
                        'past_due' => __('filament.subscriptions.table.filters.status_past_due'),
                        'canceled' => __('filament.subscriptions.table.filters.status_canceled'),
                        'free' => __('filament.subscriptions.table.filters.status_free'),
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
