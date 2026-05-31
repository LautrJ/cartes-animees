<?php

namespace App\Filament\Resources\TherapistPaymentInfos\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TherapistPaymentInfosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.first_name')
                    ->label(__('filament.therapist_payment_infos.table.columns.therapist'))
                    ->getStateUsing(fn ($record) => "{$record->user->first_name} {$record->user->last_name}")
                    ->searchable(query: fn ($query, $search) => $query->whereHas('user', fn ($q) => $q
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                    )),
                TextColumn::make('bank_name')
                    ->label(__('filament.therapist_payment_infos.table.columns.bank_name')),
                TextColumn::make('iban')
                    ->label(__('filament.therapist_payment_infos.table.columns.iban'))
                    ->getStateUsing(fn ($record) => '•••• •••• •••• '.substr($record->iban, -4)),
                TextColumn::make('bic')
                    ->label(__('filament.therapist_payment_infos.table.columns.bic')),
                TextColumn::make('created_at')
                    ->label(__('filament.therapist_payment_infos.table.columns.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
