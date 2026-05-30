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
                    ->label('Nom')
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label('Créé par')
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                TextColumn::make('duration')
                    ->label('Durée')
                    ->suffix(' sec')
                    ->placeholder('-'),
                TextColumn::make('series_count')
                    ->label('Séries')
                    ->counts('series'),
                TextColumn::make('created_at')
                    ->label('Créée le')
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
