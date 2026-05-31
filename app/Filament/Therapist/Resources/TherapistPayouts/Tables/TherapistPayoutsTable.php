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
                    ->label(__('filament.therapist.therapist_payouts.table.amount'))
                    ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                TextColumn::make('patient_count')
                    ->label(__('filament.therapist.therapist_payouts.table.patient_count')),
                TextColumn::make('commission_rate')
                    ->label(__('filament.therapist.therapist_payouts.table.commission_rate'))
                    ->getStateUsing(fn ($record) => $record->commission_rate.__('filament.commission_rate_histories.rate_suffix')),
                TextColumn::make('period_start')
                    ->label(__('filament.therapist.therapist_payouts.table.period'))
                    ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y')),
                IconColumn::make('paid_at')
                    ->label(__('filament.therapist.therapist_payouts.table.paid'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! is_null($record->paid_at)),
                TextColumn::make('paid_at')
                    ->label(__('filament.therapist.therapist_payouts.table.paid_at'))
                    ->dateTime('d/m/Y')
                    ->placeholder(__('filament.therapist.therapist_payouts.table.pending_placeholder')),
            ])
            ->filters([
                Filter::make('pending')
                    ->label(__('filament.therapist.therapist_payouts.table.filter_pending'))
                    ->query(fn ($query) => $query->whereNull('paid_at'))
                    ->default(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
