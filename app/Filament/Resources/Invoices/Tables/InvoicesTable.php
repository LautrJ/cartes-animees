<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
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
                    ->label(__('filament.invoices.table.columns.stripe_invoice_id'))
                    ->searchable(),
                TextColumn::make('subscription.child.first_name')
                    ->label(__('filament.invoices.table.columns.child'))
                    ->getStateUsing(fn ($record) => "{$record->subscription->child->first_name} {$record->subscription->child->last_name}"),
                TextColumn::make('amount')
                    ->label(__('filament.invoices.table.columns.amount'))
                    ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                TextColumn::make('status')
                    ->label(__('filament.invoices.table.columns.status'))
                    ->badge()
                    ->color(fn (InvoiceStatus $state) => match ($state) {
                        InvoiceStatus::Paid => 'success',
                        InvoiceStatus::Open => 'warning',
                        InvoiceStatus::Draft => 'gray',
                        InvoiceStatus::Uncollectible => 'danger',
                        InvoiceStatus::Void => 'gray',
                    }),
                TextColumn::make('period_start')
                    ->label(__('filament.invoices.table.columns.period'))
                    ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y')),
                TextColumn::make('paid_at')
                    ->label(__('filament.invoices.table.columns.paid_at'))
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.invoices.table.filters.status'))
                    ->options([
                        'draft' => __('filament.invoices.table.filters.status_draft'),
                        'open' => __('filament.invoices.table.filters.status_open'),
                        'paid' => __('filament.invoices.table.filters.status_paid'),
                        'uncollectible' => __('filament.invoices.table.filters.status_uncollectible'),
                        'void' => __('filament.invoices.table.filters.status_void'),
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
