<?php

namespace App\Filament\Resources\SubscriptionPriceHistories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionPriceHistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Prix d\'abonnement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('price')
                            ->label('Prix')
                            ->getStateUsing(fn($record) => number_format($record->price, 2, ',', ' ') . ' €/mois'),
                        TextEntry::make('stripe_price_id')
                            ->label('Stripe Price ID')
                            ->copyable()
                            ->copyMessage('ID copié')
                            ->fontFamily('mono')
                            ->color('gray'),
                        TextEntry::make('effective_from')
                            ->label('En vigueur depuis')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('creator.first_name')
                            ->label('Modifié par')
                            ->getStateUsing(fn($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
