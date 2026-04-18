<?php

namespace App\Filament\Resources\CommissionRateHistories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
                    ->label('Taux')
                    ->getStateUsing(fn($record) => $record->rate . ' €/patient'),
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
            ->toolbarActions([
            ]);
    }
}
