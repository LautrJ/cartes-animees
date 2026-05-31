<?php

namespace App\Filament\Therapist\Resources\Series\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.therapist.series.table.name'))
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label(__('filament.therapist.series.table.created_by'))
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                IconColumn::make('is_base')
                    ->label(__('filament.therapist.series.table.is_base'))
                    ->boolean(),
                TextColumn::make('cards_count')
                    ->label(__('filament.therapist.series.table.cards_count'))
                    ->counts('cards'),
                TextColumn::make('created_at')
                    ->label(__('filament.therapist.series.table.created_at'))
                    ->dateTime('d/m/Y')
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
