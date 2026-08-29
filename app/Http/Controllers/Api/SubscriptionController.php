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
use Stripe\Exception\CardException;

class SubscriptionController extends Controller
{
    public function __construct(protected StripeService $stripeService) {}

    /**
     * Créer un abonnement
     */
    public function store(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $validated = $request->validate([
            'payment_method_id' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Vérifier qu'il n'y a pas déjà un abonnement actif ou en attente
        $existingSubscription = Subscription::where('child_id', $child->id)
            ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::Incomplete, SubscriptionStatus::PastDue])
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === SubscriptionStatus::Active) {
                return ApiResponse::error(__('api.subscription.already_active'), 409);
            }

            // Incomplete (premier paiement jamais abouti) ou PastDue (renouvellement en échec) :
            // dans les deux cas on réutilise le même abonnement Stripe. On rattache le nouveau
            // moyen de paiement et on renvoie le client_secret du PaymentIntent en attente.
            $this->stripeService->attachPaymentMethod(
                $user->stripe_customer_id,
                $validated['payment_method_id']
            );

            $clientSecret = $this->stripeService->getClientSecretForSubscription(
                $existingSubscription->stripe_subscription_id,
                $user->stripe_customer_id
            );

            // Aucun PI en attente : soit le paiement a déjà abouti entre-temps (ex. webhook en
            // retard/absent, double appel pendant qu'une confirmation 3DS précédente se terminait),
            // soit il a échoué. On vérifie le statut réel sur Stripe pour resynchroniser la BDD
            // au lieu de laisser l'abonnement bloqué en "incomplete" indéfiniment.
            if (! $clientSecret) {
                $freshSub = $this->stripeService->retrieveSubscription($existingSubscription->stripe_subscription_id);
                if ($freshSub->status === 'active') {
                    $existingSubscription->update(['status' => SubscriptionStatus::Active]);
                    $existingSubscription->refresh();
                }
            }

            return ApiResponse::success([
                'subscription' => $existingSubscription,
                'client_secret' => $clientSecret,
            ]);
        }

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
                return ApiResponse::error(__('api.subscription.no_price_configured'), 500);
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
                'status' => SubscriptionStatus::Incomplete,
                'current_period_start' => $stripeSubscription->current_period_start
                    ? now()->createFromTimestamp($stripeSubscription->current_period_start)
                    : now(),
                'current_period_end' => $stripeSubscription->current_period_end
                    ? now()->createFromTimestamp($stripeSubscription->current_period_end)
                    : now()->addMonth(),
            ]);

            $clientSecret = $this->stripeService->getClientSecretForSubscription(
                $stripeSubscription->id,
                $user->stripe_customer_id
            );

            // Si aucun PI en attente n'est trouvé, Stripe a peut-être déjà traité le paiement
            // (cas d'une carte sans 3DS avec une méthode de paiement par défaut déjà attachée)
            if (! $clientSecret) {
                $freshSub = $this->stripeService->retrieveSubscription($stripeSubscription->id);
                if ($freshSub->status === 'active') {
                    $subscription->update(['status' => SubscriptionStatus::Active]);
                    $subscription->refresh();
                }
            }

            return ApiResponse::success([
                'subscription' => $subscription,
                'client_secret' => $clientSecret,
            ], 201);

        } catch (CardException $e) {
            $code = $e->getError()->decline_code ?? $e->getError()->code ?? 'card_declined';
            $message = match ($code) {
                'insufficient_funds' => 'Fonds insuffisants sur votre carte.',
                'card_declined' => 'Votre carte a été refusée.',
                'incorrect_cvc' => 'Le code de sécurité (CVC) est incorrect.',
                'expired_card' => 'Votre carte a expiré.',
                'incorrect_number' => 'Le numéro de carte est incorrect.',
                'invalid_expiry_month' => 'Le mois d\'expiration est invalide.',
                'invalid_expiry_year' => 'L\'année d\'expiration est invalide.',
                'do_not_honor',
                'generic_decline' => 'Votre carte a été refusée. Contactez votre banque.',
                'lost_card',
                'stolen_card' => 'Votre carte a été refusée.',
                default => 'Votre carte a été refusée. Veuillez réessayer ou utiliser une autre carte.',
            };

            return ApiResponse::error($message, 422);
        } catch (\Exception $e) {
            Log::error('Erreur création abonnement Stripe', [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(__('api.subscription.creation_error'), 500);
        }
    }

    /**
     * Annuler un abonnement
     */
    public function destroy(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $subscription = Subscription::where('child_id', $child->id)
            ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::PastDue])
            ->first();

        if (! $subscription) {
            return ApiResponse::error(__('api.subscription.no_active_subscription'), 404);
        }

        try {
            $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);

            $subscription->update([
                'status' => SubscriptionStatus::Canceled,
                'canceled_at' => now(),
            ]);

            return ApiResponse::success(['message' => __('api.subscription.cancel_success')]);

        } catch (\Exception $e) {
            Log::error('Erreur annulation abonnement Stripe', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(__('api.subscription.cancel_error'), 500);
        }
    }

    /**
     * Voir l'abonnement d'un enfant
     */
    public function show(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $subscription = Subscription::where('child_id', $child->id)
            ->latest()
            ->first();

        if (! $subscription) {
            return ApiResponse::error(__('api.subscription.not_found'), 404);
        }

        return ApiResponse::success($subscription);
    }

    /**
     * Renvoie le prix actuel d'un abonnement
     */
    public function currentPrice(): JsonResponse
    {
        $price = SubscriptionPriceHistory::orderBy('effective_from', 'desc')->first();

        return ApiResponse::success(['price' => $price?->price]);
    }
}
