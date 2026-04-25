<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class TherapistPayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('amount')
                    ->label('Montant')
                    ->getStateUsing(fn($record) => number_format($record->amount, 2) . ' €'),
                TextColumn::make('patient_count')
                    ->label('Patients'),
                TextColumn::make('commission_rate')
                    ->label('Taux')
                    ->getStateUsing(fn($record) => $record->commission_rate . ' €/patient'),
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
                    ->placeholder('En attente'),
            ])
            ->filters([
                Filter::make('pending')
                    ->label('En attente')
                    ->query(fn($query) => $query->whereNull('paid_at'))
                    ->default(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
