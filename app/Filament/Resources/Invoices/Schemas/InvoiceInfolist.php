<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.invoices.infolist.sections.child_subscription'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subscription.child.first_name')
                            ->label(__('filament.invoices.infolist.fields.child'))
                            ->getStateUsing(fn ($record) => "{$record->subscription->child->first_name} {$record->subscription->child->last_name}"),
                        TextEntry::make('subscription.stripe_subscription_id')
                            ->label(__('filament.invoices.infolist.fields.stripe_subscription_id')),
                    ]),

                Section::make(__('filament.invoices.infolist.sections.invoice'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('stripe_invoice_id')
                            ->label(__('filament.invoices.infolist.fields.stripe_invoice_id'))
                            ->columnSpanFull(),
                        TextEntry::make('amount')
                            ->label(__('filament.invoices.infolist.fields.amount'))
                            ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                        TextEntry::make('status')
                            ->label(__('filament.invoices.infolist.fields.status'))
                            ->badge()
                            ->color(fn (InvoiceStatus $state) => match ($state) {
                                InvoiceStatus::Paid => 'success',
                                InvoiceStatus::Open => 'warning',
                                InvoiceStatus::Draft => 'gray',
                                InvoiceStatus::Uncollectible => 'danger',
                                InvoiceStatus::Void => 'gray',
                            }),
                        TextEntry::make('period_start')
                            ->label(__('filament.invoices.infolist.fields.period'))
                            ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y'))
                            ->columnSpanFull(),
                        TextEntry::make('paid_at')
                            ->label(__('filament.invoices.infolist.fields.paid_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder(__('filament.invoices.infolist.fields.paid_at_placeholder')),
                        TextEntry::make('invoice_pdf')
                            ->label(__('filament.invoices.infolist.fields.invoice_pdf'))
                            ->placeholder(__('filament.invoices.infolist.fields.invoice_pdf_placeholder')),
                    ]),

                Section::make(__('filament.invoices.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.invoices.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.invoices.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
