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
                Section::make('Enfant & abonnement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subscription.child.first_name')
                            ->label('Enfant')
                            ->getStateUsing(fn ($record) => "{$record->subscription->child->first_name} {$record->subscription->child->last_name}"),
                        TextEntry::make('subscription.stripe_subscription_id')
                            ->label('Abonnement Stripe'),
                    ]),

                Section::make('Facture')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('stripe_invoice_id')
                            ->label('ID Stripe')
                            ->columnSpanFull(),
                        TextEntry::make('amount')
                            ->label('Montant')
                            ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (InvoiceStatus $state) => match ($state) {
                                InvoiceStatus::Paid => 'success',
                                InvoiceStatus::Open => 'warning',
                                InvoiceStatus::Draft => 'gray',
                                InvoiceStatus::Uncollectible => 'danger',
                                InvoiceStatus::Void => 'gray',
                            }),
                        TextEntry::make('period_start')
                            ->label('Période')
                            ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y'))
                            ->columnSpanFull(),
                        TextEntry::make('paid_at')
                            ->label('Payé le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Non payé'),
                        TextEntry::make('invoice_pdf')
                            ->label('PDF')
                            ->placeholder('Aucun fichier'),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
