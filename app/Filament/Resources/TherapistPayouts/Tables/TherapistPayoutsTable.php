<?php

namespace App\Filament\Resources\TherapistPayouts\Tables;

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

class TherapistPayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                TextColumn::make('therapist.first_name')
                    ->label('Orthophoniste')
                    ->getStateUsing(fn($record) => "{$record->therapist->first_name} {$record->therapist->last_name}"),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->getStateUsing(fn($record) => number_format($record->amount, 2) . ' €'),
                TextColumn::make('patient_count')
                    ->label('Patients'),
                TextColumn::make('period_start')
                    ->label('Période')
                    ->getStateUsing(fn($record) => $record->period_start->format('d/m/Y') . ' → ' . $record->period_end->format('d/m/Y')),
                IconColumn::make('paid_at')
                    ->label('Payé')
                    ->boolean()
                    ->getStateUsing(fn($record) => !is_null($record->paid_at)),
                TextColumn::make('paid_at')
                    ->label('Payé le')
                    ->dateTime('d/m/Y')
                    ->placeholder('En attente')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('pending')
                    ->label('En attente uniquement')
                    ->query(fn($query) => $query->whereNull('paid_at'))
                    ->default(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
