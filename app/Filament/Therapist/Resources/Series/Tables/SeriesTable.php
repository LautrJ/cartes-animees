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
                    ->label('Nom')
                    ->getStateUsing(fn($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label('Créé par')
                    ->getStateUsing(fn($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                IconColumn::make('is_base')
                    ->label('Série de base')
                    ->boolean(),
                TextColumn::make('cards_count')
                    ->label('Cartes')
                    ->counts('cards'),
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
