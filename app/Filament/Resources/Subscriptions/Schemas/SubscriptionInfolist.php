<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Enums\SubscriptionStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([1])
            ->components([
                Section::make('Enfant & parent')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('child.first_name')
                            ->label('Enfant')
                            ->getStateUsing(fn($record) => "{$record->child->first_name} {$record->child->last_name}"),
                        TextEntry::make('child.parent.first_name')
                            ->label('Parent')
                            ->getStateUsing(fn($record) => "{$record->child->parent->first_name} {$record->child->parent->last_name}"),
                    ]),

                Section::make('Abonnement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn(SubscriptionStatus $state) => match($state) {
                                SubscriptionStatus::Active   => 'success',
                                SubscriptionStatus::PastDue  => 'danger',
                                SubscriptionStatus::Canceled => 'gray',
                                SubscriptionStatus::Free     => 'info',
                            }),
                        TextEntry::make('override_price')
                            ->label('Prix')
                            ->getStateUsing(fn($record) => match(true) {
                                $record->override_price === null        => 'Prix normal',
                                (float)$record->override_price === 0.0 => 'Gratuit',
                                default => number_format($record->override_price, 2) . ' €',
                            }),
                        TextEntry::make('stripe_subscription_id')
                            ->label('ID Stripe')
                            ->placeholder('Aucun (gratuit)')
                            ->columnSpanFull(),
                        TextEntry::make('current_period_start')
                            ->label('Début de période')
                            ->dateTime('d/m/Y'),
                        TextEntry::make('current_period_end')
                            ->label('Fin de période')
                            ->dateTime('d/m/Y'),
                        TextEntry::make('canceled_at')
                            ->label('Annulé le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('overriddenBy.first_name')
                            ->label('Remise appliquée par')
                            ->getStateUsing(fn($record) => $record->overriddenBy
                                ? "{$record->overriddenBy->first_name} {$record->overriddenBy->last_name}"
                                : '-'
                            ),
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
