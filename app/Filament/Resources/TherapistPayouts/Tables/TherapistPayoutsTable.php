<?php

namespace App\Filament\Resources\TherapistPayouts\Tables;

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
                    ->label(__('filament.therapist_payouts.table.columns.therapist'))
                    ->getStateUsing(fn ($record) => "{$record->therapist->first_name} {$record->therapist->last_name}"),
                TextColumn::make('amount')
                    ->label(__('filament.therapist_payouts.table.columns.amount'))
                    ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                TextColumn::make('patient_count')
                    ->label(__('filament.therapist_payouts.table.columns.patient_count')),
                TextColumn::make('period_start')
                    ->label(__('filament.therapist_payouts.table.columns.period'))
                    ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y')),
                IconColumn::make('paid_at')
                    ->label(__('filament.therapist_payouts.table.columns.paid'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! is_null($record->paid_at)),
                TextColumn::make('paid_at')
                    ->label(__('filament.therapist_payouts.table.columns.paid_at'))
                    ->dateTime('d/m/Y')
                    ->placeholder(__('filament.therapist_payouts.table.columns.paid_at_placeholder'))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('pending')
                    ->label(__('filament.therapist_payouts.table.filters.pending'))
                    ->query(fn ($query) => $query->whereNull('paid_at'))
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
