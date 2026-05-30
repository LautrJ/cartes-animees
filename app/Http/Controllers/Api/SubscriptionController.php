<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Child;
use App\Models\Subscription;
use App\Models\SubscriptionPriceHistory;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(protected StripeService $stripeService) {}

    /**
     * Créer un abonnement
     */
    public function store(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        // Vérifier qu'il n'y a pas déjà un abonnement actif
        $existingSubscription = Subscription::where('child_id', $child->id)
            ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::PastDue])
            ->first();

        if ($existingSubscription) {
            return ApiResponse::error('Cet enfant a déjà un abonnement actif.', 409);
        }

        $validated = $request->validate([
            'payment_method_id' => ['required', 'string'],
        ]);

        $user = $request->user();

        try {
            // Créer ou récupérer le customer Stripe
            if (! $user->stripe_customer_id) {
                $customerId = $this->stripeService->createCustomer(
                    $user->email,
                    "{$user->first_name} {$user->last_name}"
                );
                $user->update(['stripe_customer_id' => $customerId]);
            }

            // Attacher le payment method au customer
            $this->stripeService->attachPaymentMethod(
                $user->stripe_customer_id,
                $validated['payment_method_id']
            );

            // Récupérer le stripe_price_id actif
            $latestPrice = SubscriptionPriceHistory::orderBy('effective_from', 'desc')->first();

            if (! $latestPrice) {
                return ApiResponse::error('Aucun tarif configuré.', 500);
            }

            // Créer la subscription Stripe
            $stripeSubscription = $this->stripeService->createSubscription(
                $user->stripe_customer_id,
                $latestPrice->stripe_price_id,
            );

            // Stocker la subscription en base
            $subscription = Subscription::create([
                'child_id' => $child->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'stripe_price_id' => $latestPrice->stripe_price_id,
                'status' => SubscriptionStatus::Active,
                'current_period_start' => $stripeSubscription->current_period_start
                    ? now()->createFromTimestamp($stripeSubscription->current_period_start)
                    : now(),
                'current_period_end' => $stripeSubscription->current_period_end
                    ? now()->createFromTimestamp($stripeSubscription->current_period_end)
                    : now()->addMonth(),
            ]);

            return ApiResponse::success([
                'subscription' => $subscription,
                'client_secret' => $stripeSubscription->latest_invoice->payment_intent->client_secret ?? null,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur création abonnement Stripe', [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error('Erreur lors de la création de l\'abonnement.', 500);
        }
    }

    /**
     * Annuler un abonnement
     */
    public function destroy(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $subscription = Subscription::where('child_id', $child->id)
            ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::PastDue])
            ->first();

        if (! $subscription) {
            return ApiResponse::error('Aucun abonnement actif pour cet enfant.', 404);
        }

        try {
            $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);

            $subscription->update([
                'status' => SubscriptionStatus::Canceled,
                'canceled_at' => now(),
            ]);

            return ApiResponse::success(['message' => 'Abonnement annulé avec succès.']);

        } catch (\Exception $e) {
            Log::error('Erreur annulation abonnement Stripe', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error('Erreur lors de l\'annulation de l\'abonnement.', 500);
        }
    }

    /**
     * Voir l'abonnement d'un enfant
     */
    public function show(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $subscription = Subscription::where('child_id', $child->id)
            ->latest()
            ->first();

        if (! $subscription) {
            return ApiResponse::error('Aucun abonnement trouvé.', 404);
        }

        return ApiResponse::success($subscription);
    }
}
