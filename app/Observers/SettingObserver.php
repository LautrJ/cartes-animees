<?php

namespace App\Observers;

use App\Models\CommissionRateHistory;
use App\Models\Setting;
use App\Models\SubscriptionPriceHistory;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingObserver
{
    public function __construct(protected StripeService $stripeService) {}

    public function updated(Setting $setting): void
    {
        match ($setting->key) {
            'commission_rate' => $this->handleCommissionRateChange($setting),
            'subscription_price' => $this->handleSubscriptionPriceChange($setting),
            default => null,
        };
    }

    private function handleCommissionRateChange(Setting $setting): void
    {
        $adminId = Auth::id() ?? User::where('role', 'admin')->first()?->id;

        CommissionRateHistory::create([
            'rate' => $setting->value,
            'effective_from' => now(),
            'created_by' => $adminId,
        ]);
    }

    private function handleSubscriptionPriceChange(Setting $setting): void
    {
        $adminId = Auth::id() ?? User::where('role', 'admin')->first()?->id;

        try {
            $stripePriceId = $this->stripeService->createPrice((float) $setting->value);

            SubscriptionPriceHistory::create([
                'price' => $setting->value,
                'stripe_price_id' => $stripePriceId,
                'effective_from' => now(),
                'created_by' => $adminId,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur création Price Stripe : '.$e->getMessage());
        }
    }
}
