<?php

namespace App\Filament\Resources\Cards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.cards.table.columns.name'))
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label(__('filament.cards.table.columns.creator'))
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                TextColumn::make('duration')
                    ->label(__('filament.cards.table.columns.duration'))
                    ->suffix(' sec')
                    ->default('-'),
                IconColumn::make('is_validated')
                    ->label(__('filament.cards.table.columns.is_validated'))
                    ->boolean(),
                TextColumn::make('series_count')
                    ->label(__('filament.cards.table.columns.series_count'))
                    ->counts('series'),
                TextColumn::make('created_at')
                    ->label(__('filament.cards.table.columns.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_validated')
                    ->label(__('filament.cards.table.filters.is_validated'))
                    ->query(fn ($query) => $query->where('is_validated', true)),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
