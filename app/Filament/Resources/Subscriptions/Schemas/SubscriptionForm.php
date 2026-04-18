<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Enums\SubscriptionStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Statut')
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active'   => 'Actif',
                                'past_due' => 'En retard',
                                'canceled' => 'Annulé',
                                'free'     => 'Gratuit',
                            ])
                            ->required(),
                    ]),

                Section::make('Remise & gratuité')
                    ->schema([
                        TextInput::make('override_price')
                            ->label('Prix personnalisé (€)')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Laisser vide = prix normal, 0 = gratuit')
                            ->nullable(),
                    ]),
            ]);
    }
}
