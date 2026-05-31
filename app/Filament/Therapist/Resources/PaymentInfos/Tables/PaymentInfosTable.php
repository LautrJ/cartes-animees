<?php

namespace App\Filament\Therapist\Resources\PaymentInfos\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentInfosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')
                    ->label(__('filament.therapist.payment_infos.table.bank_name')),
                TextColumn::make('bic')
                    ->label(__('filament.therapist.payment_infos.table.bic')),
                TextColumn::make('iban')
                    ->label(__('filament.therapist.payment_infos.table.iban'))
                    ->getStateUsing(fn ($record) => '•••• •••• •••• '.substr($record->iban, -4)),
                TextColumn::make('updated_at')
                    ->label(__('filament.therapist.payment_infos.table.updated_at'))
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
