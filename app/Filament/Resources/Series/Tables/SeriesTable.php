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
                    ->label('Nom')
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? '-')
                    ->searchable(),
                TextColumn::make('creator.first_name')
                    ->label('Créé par')
                    ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                IconColumn::make('is_base')
                    ->label('Série de base')
                    ->boolean(),
                IconColumn::make('is_validated')
                    ->label('Validée')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Active')
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
            ->filters([
                Filter::make('is_base')
                    ->label('Séries de base')
                    ->query(fn ($query) => $query->where('is_base', true)),
                Filter::make('is_validated')
                    ->label('Validées')
                    ->query(fn ($query) => $query->where('is_validated', true)),
                Filter::make('is_active')
                    ->label('Actives')
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
