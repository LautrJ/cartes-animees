<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('stripe_invoice_id')
                    ->label('ID Stripe')
                    ->searchable(),
                TextColumn::make('subscription.child.first_name')
                    ->label('Enfant')
                    ->getStateUsing(fn($record) => "{$record->subscription->child->first_name} {$record->subscription->child->last_name}"),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->getStateUsing(fn($record) => number_format($record->amount, 2) . ' €'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn(InvoiceStatus $state) => match($state) {
                        InvoiceStatus::Paid          => 'success',
                        InvoiceStatus::Open          => 'warning',
                        InvoiceStatus::Draft         => 'gray',
                        InvoiceStatus::Uncollectible => 'danger',
                        InvoiceStatus::Void          => 'gray',
                    }),
                TextColumn::make('period_start')
                    ->label('Période')
                    ->getStateUsing(fn($record) => $record->period_start->format('d/m/Y') . ' → ' . $record->period_end->format('d/m/Y')),
                TextColumn::make('paid_at')
                    ->label('Payé le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft'         => 'Brouillon',
                        'open'          => 'Ouvert',
                        'paid'          => 'Payé',
                        'uncollectible' => 'Irrécupérable',
                        'void'          => 'Annulé',
                    ])
                    ->default('open'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
