<?php

namespace App\Observers;

use App\Enums\ChildSeriesStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Child;
use App\Models\Series;
use App\Models\Subscription;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChildObserver
{
    public function __construct(protected StripeService $stripeService) {}

    public function deleting(Child $child): void
    {
        $subscription = Subscription::where('child_id', $child->id)
            ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::Incomplete, SubscriptionStatus::PastDue])
            ->first();

        if ($subscription && $subscription->stripe_subscription_id) {
            try {
                $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);
                $subscription->delete();
            } catch (\Exception $e) {
                Log::error('Impossible d\'annuler l\'abonnement Stripe lors de la suppression de l\'enfant', [
                    'child_id'        => $child->id,
                    'subscription_id' => $subscription->stripe_subscription_id,
                    'error'           => $e->getMessage(),
                ]);
            }

            $subscription->update([
                'status'      => SubscriptionStatus::Canceled,
                'canceled_at' => now(),
            ]);
        }
    }

    public function created(Child $child): void
    {
        $baseSeries = Series::base()->validated()->active()->get();

        $now = now();

        $rows = $baseSeries->map(fn (Series $series) => [
            'child_id' => $child->id,
            'series_id' => $series->id,
            'unlocked_by' => null,
            'status' => ChildSeriesStatus::Unlocked->value,
            'unlocked_at' => $now,
            'completed_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        if (! empty($rows)) {
            DB::table('child_series')->insert($rows);
        }
    }
}
