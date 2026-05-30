<?php

namespace App\Filament\Resources\Children\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(query: function ($query, $search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    }),
                TextColumn::make('parent.first_name')
                    ->label('Parent')
                    ->getStateUsing(fn ($record) => "{$record->parent->first_name} {$record->parent->last_name}")
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('parent', fn ($q) => $q
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                        );
                    }),
                TextColumn::make('birthdate')
                    ->label('Date de naissance')
                    ->date('d/m/Y')
                    ->default('-'),
                TextColumn::make('activeTherapists.first_name')
                    ->label('Orthophonistes')
                    ->getStateUsing(fn ($record) => $record->activeTherapists
                        ->map(fn ($t) => "{$t->first_name} {$t->last_name}")
                        ->join(', ') ?: '-'
                    ),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
