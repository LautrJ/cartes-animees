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
                Section::make(__('filament.subscriptions.infolist.sections.child_parent'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('child.first_name')
                            ->label(__('filament.subscriptions.infolist.fields.child'))
                            ->getStateUsing(fn ($record) => "{$record->child->first_name} {$record->child->last_name}"),
                        TextEntry::make('child.parent.first_name')
                            ->label(__('filament.subscriptions.infolist.fields.parent'))
                            ->getStateUsing(fn ($record) => "{$record->child->parent->first_name} {$record->child->parent->last_name}"),
                    ]),

                Section::make(__('filament.subscriptions.infolist.sections.subscription'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('filament.subscriptions.infolist.fields.status'))
                            ->badge()
                            ->color(fn (SubscriptionStatus $state) => match ($state) {
                                SubscriptionStatus::Active => 'success',
                                SubscriptionStatus::PastDue => 'danger',
                                SubscriptionStatus::Canceled => 'gray',
                                SubscriptionStatus::Free => 'info',
                            }),
                        TextEntry::make('override_price')
                            ->label(__('filament.subscriptions.infolist.fields.price'))
                            ->getStateUsing(fn ($record) => match (true) {
                                $record->override_price === null => __('filament.subscriptions.infolist.fields.price_normal'),
                                (float) $record->override_price === 0.0 => __('filament.subscriptions.infolist.fields.price_free'),
                                default => number_format($record->override_price, 2).' €',
                            }),
                        TextEntry::make('stripe_subscription_id')
                            ->label(__('filament.subscriptions.infolist.fields.stripe_subscription_id'))
                            ->placeholder(__('filament.subscriptions.infolist.fields.stripe_subscription_id_placeholder'))
                            ->columnSpanFull(),
                        TextEntry::make('current_period_start')
                            ->label(__('filament.subscriptions.infolist.fields.current_period_start'))
                            ->dateTime('d/m/Y'),
                        TextEntry::make('current_period_end')
                            ->label(__('filament.subscriptions.infolist.fields.current_period_end'))
                            ->dateTime('d/m/Y'),
                        TextEntry::make('canceled_at')
                            ->label(__('filament.subscriptions.infolist.fields.canceled_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('overriddenBy.first_name')
                            ->label(__('filament.subscriptions.infolist.fields.overridden_by'))
                            ->getStateUsing(fn ($record) => $record->overriddenBy
                                ? "{$record->overriddenBy->first_name} {$record->overriddenBy->last_name}"
                                : '-'
                            ),
                    ]),

                Section::make(__('filament.subscriptions.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.subscriptions.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.subscriptions.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
