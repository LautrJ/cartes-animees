<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Enums\SubscriptionStatus;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Models\Setting;
use App\Notifications\DiscountAppliedNotification;
use App\Services\StripeService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('apply_discount')
                ->label(__('filament.subscriptions.actions.apply_discount.label'))
                ->icon('heroicon-o-tag')
                ->color('warning')
                ->visible(fn () => $this->getRecord()->status === SubscriptionStatus::Active
                    && filled($this->getRecord()->stripe_subscription_id)
                    && blank($this->getRecord()->stripe_coupon_id)
                )
                ->schema([
                    TextInput::make('discount_amount')
                        ->label(__('filament.subscriptions.actions.apply_discount.field_discount_amount'))
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(fn () => (float) Setting::where('key', 'subscription_price')->first()?->value ?? 9.99)
                        ->required(),
                ])
                ->modalHeading(__('filament.subscriptions.actions.apply_discount.modal_heading'))
                ->modalDescription(__('filament.subscriptions.actions.apply_discount.modal_description'))
                ->modalSubmitActionLabel(__('filament.subscriptions.actions.apply_discount.modal_submit_label'))
                ->action(function (array $data) {
                    $subscription      = $this->getRecord();
                    $subscriptionPrice = (float) Setting::where('key', 'subscription_price')->first()?->value ?? 9.99;
                    $discountAmount    = (float) $data['discount_amount'];
                    $newPrice          = round($subscriptionPrice - $discountAmount, 2);

                    $stripeService = app(StripeService::class);
                    $couponId      = $stripeService->createCoupon($discountAmount);
                    $stripeService->applyCouponToSubscription($subscription->stripe_subscription_id, $couponId);

                    $subscription->update([
                        'override_price'   => $newPrice,
                        'overridden_by'    => Auth::id(),
                        'stripe_coupon_id' => $couponId,
                    ]);

                    $subscription->child->parent->notify(
                        new DiscountAppliedNotification($subscription->child, $discountAmount, $newPrice)
                    );

                    Notification::make()
                        ->title(__('filament.subscriptions.actions.apply_discount.notification_success', [
                            'amount' => $discountAmount,
                        ]))
                        ->success()
                        ->send();
                }),

            Action::make('remove_discount')
                ->label(__('filament.subscriptions.actions.remove_discount.label'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => filled($this->getRecord()->stripe_coupon_id))
                ->requiresConfirmation()
                ->modalHeading(__('filament.subscriptions.actions.remove_discount.modal_heading'))
                ->modalDescription(__('filament.subscriptions.actions.remove_discount.modal_description'))
                ->modalSubmitActionLabel(__('filament.subscriptions.actions.remove_discount.modal_submit_label'))
                ->action(function () {
                    $subscription = $this->getRecord();

                    app(StripeService::class)->removeCouponFromSubscription(
                        $subscription->stripe_subscription_id
                    );

                    $subscription->update([
                        'override_price'   => null,
                        'overridden_by'    => null,
                        'stripe_coupon_id' => null,
                    ]);

                    Notification::make()
                        ->title(__('filament.subscriptions.actions.remove_discount.notification_success'))
                        ->success()
                        ->send();
                }),

            Action::make('cancel_free_subscription')
                ->label(__('filament.subscriptions.actions.cancel_free_subscription.label'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->getRecord()->status === SubscriptionStatus::Free)
                ->requiresConfirmation()
                ->modalHeading(__('filament.subscriptions.actions.cancel_free_subscription.modal_heading'))
                ->modalDescription(__('filament.subscriptions.actions.cancel_free_subscription.modal_description'))
                ->modalSubmitActionLabel(__('filament.subscriptions.actions.cancel_free_subscription.modal_submit_label'))
                ->action(function () {
                    $this->getRecord()->update([
                        'status'         => SubscriptionStatus::Canceled,
                        'canceled_at'    => now(),
                        'override_price' => null,
                        'overridden_by'  => null,
                    ]);

                    Notification::make()
                        ->title(__('filament.subscriptions.actions.cancel_free_subscription.notification_success'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
