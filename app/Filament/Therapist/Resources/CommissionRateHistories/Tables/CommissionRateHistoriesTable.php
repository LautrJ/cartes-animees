<?php

namespace App\Filament\Therapist\Resources\CommissionRateHistories\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommissionRateHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('effective_from', 'desc')
            ->columns([
                TextColumn::make('rate')
                    ->label(__('filament.therapist.commission_rate_histories.table.rate'))
                    ->getStateUsing(fn ($record) => $record->rate.__('filament.therapist.commission_rate_histories.table.rate_suffix')),
                TextColumn::make('effective_from')
                    ->label(__('filament.therapist.commission_rate_histories.table.effective_from'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('creator.first_name')
                    ->label(__('filament.therapist.commission_rate_histories.table.modified_by'))
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                TextColumn::make('created_at')
                    ->label(__('filament.therapist.commission_rate_histories.table.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
