<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.subscriptions.form.sections.status'))
                    ->schema([
                        Select::make('status')
                            ->label(__('filament.subscriptions.form.fields.status'))
                            ->options([
                                'active' => __('filament.subscriptions.form.fields.status_active'),
                                'past_due' => __('filament.subscriptions.form.fields.status_past_due'),
                                'canceled' => __('filament.subscriptions.form.fields.status_canceled'),
                                'free' => __('filament.subscriptions.form.fields.status_free'),
                            ])
                            ->required(),
                    ]),

                Section::make(__('filament.subscriptions.form.sections.discount'))
                    ->schema([
                        TextInput::make('override_price')
                            ->label(__('filament.subscriptions.form.fields.override_price'))
                            ->numeric()
                            ->minValue(0)
                            ->placeholder(__('filament.subscriptions.form.fields.override_price_placeholder'))
                            ->nullable(),
                    ]),
            ]);
    }
}
