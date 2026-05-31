<?php

namespace App\Filament\Resources\Children\Pages;

use App\Enums\SubscriptionStatus;
use App\Filament\Resources\Children\ChildResource;
use App\Models\Subscription;
use App\Notifications\FreeSubscriptionCreatedNotification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewChild extends ViewRecord
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('create_free_subscription')
                ->label(__('filament.children.actions.create_free_subscription.label'))
                ->icon('heroicon-o-gift')
                ->color('success')
                ->visible(fn () => ! Subscription::where('child_id', $this->getRecord()->id)
                    ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::Free])
                    ->exists()
                )
                ->requiresConfirmation()
                ->modalHeading(__('filament.children.actions.create_free_subscription.modal_heading'))
                ->modalDescription(__('filament.children.actions.create_free_subscription.modal_description'))
                ->modalSubmitActionLabel(__('filament.children.actions.create_free_subscription.modal_submit_label'))
                ->action(function () {
                    $child  = $this->getRecord();
                    $parent = $child->parent;

                    Subscription::create([
                        'child_id'               => $child->id,
                        'overridden_by'          => Auth::id(),
                        'stripe_subscription_id' => null,
                        'stripe_price_id'        => null,
                        'stripe_coupon_id'       => null,
                        'status'                 => SubscriptionStatus::Free,
                        'override_price'         => 0.00,
                        'current_period_start'   => now()->startOfMonth(),
                        'current_period_end'     => now()->endOfMonth(),
                    ]);

                    $parent->notify(new FreeSubscriptionCreatedNotification($child));

                    Notification::make()
                        ->title(__('filament.children.actions.create_free_subscription.notification_success'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
