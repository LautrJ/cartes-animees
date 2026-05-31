<?php

namespace App\Filament\Therapist\Resources\Cards\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.therapist.cards.table.name'))
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label(__('filament.therapist.cards.table.created_by'))
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                TextColumn::make('duration')
                    ->label(__('filament.therapist.cards.table.duration'))
                    ->suffix(' sec')
                    ->placeholder('-'),
                TextColumn::make('series_count')
                    ->label(__('filament.therapist.cards.table.series_count'))
                    ->counts('series'),
                TextColumn::make('created_at')
                    ->label(__('filament.therapist.cards.table.created_at'))
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
