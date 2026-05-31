<?php

namespace App\Filament\Resources\Series\Tables;

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

class SeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.series.table.columns.name'))
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label(__('filament.series.table.columns.creator'))
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                IconColumn::make('is_base')
                    ->label(__('filament.series.table.columns.is_base'))
                    ->boolean(),
                IconColumn::make('is_validated')
                    ->label(__('filament.series.table.columns.is_validated'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(__('filament.series.table.columns.is_active'))
                    ->boolean(),
                TextColumn::make('cards_count')
                    ->label(__('filament.series.table.columns.cards_count'))
                    ->counts('cards'),
                TextColumn::make('created_at')
                    ->label(__('filament.series.table.columns.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_base')
                    ->label(__('filament.series.table.filters.is_base'))
                    ->query(fn ($query) => $query->where('is_base', true)),
                Filter::make('is_validated')
                    ->label(__('filament.series.table.filters.is_validated'))
                    ->query(fn ($query) => $query->where('is_validated', true)),
                Filter::make('is_active')
                    ->label(__('filament.series.table.filters.is_active'))
                    ->query(fn ($query) => $query->where('is_active', true)),
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
