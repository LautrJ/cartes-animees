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
                    ->label('Banque'),
                TextColumn::make('bic')
                    ->label('BIC'),
                TextColumn::make('iban')
                    ->label('IBAN')
                    ->getStateUsing(fn($record) => '•••• •••• •••• ' . substr($record->iban, -4)),
                TextColumn::make('updated_at')
                    ->label('Mis à jour le')
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
