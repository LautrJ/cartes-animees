<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Child;
use App\Models\Subscription;
use App\Models\SubscriptionPriceHistory;
use App\Models\User;
use Stripe\StripeClient;

class StripeTestDataService
{
    protected StripeClient $stripe;

    public function __construct(protected StripeService $stripeService)
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    // -------------------------------------------------------------------------
    // Nettoyage sandbox
    // -------------------------------------------------------------------------

    public function archiveAllTestData(): void
    {
        $subscriptions = $this->stripe->subscriptions->all(['limit' => 100]);
        foreach ($subscriptions->data as $subscription) {
            $this->stripe->subscriptions->cancel($subscription->id);
        }

        $products = $this->stripe->products->all(['limit' => 100]);
        foreach ($products->data as $product) {
            $this->stripe->products->update($product->id, [
                'default_price' => null,
            ]);

            $prices = $this->stripe->prices->all(['product' => $product->id, 'limit' => 100]);
            foreach ($prices->data as $price) {
                if ($price->active) {
                    $this->stripe->prices->update($price->id, ['active' => false]);
                }
            }

            $this->stripe->products->update($product->id, ['active' => false]);
        }
    }

    // -------------------------------------------------------------------------
    // Payment methods de test
    // -------------------------------------------------------------------------

    public function createTestPaymentMethod(): string
    {
        $paymentMethod = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => ['token' => 'tok_visa'],
        ]);

        return $paymentMethod->id;
    }

    public function createTestPaymentMethodDecline(): string
    {
        return 'pm_card_chargeDeclinedInsufficientFunds';
    }

    // -------------------------------------------------------------------------
    // Création des abonnements de test
    // -------------------------------------------------------------------------

    public function createActiveSubscription(User $parent, Child $child, string $priceId): void
    {
        $customerId = $this->stripeService->createCustomer(
            $parent->email,
            $parent->getFilamentName()
        );

        $parent->update(['stripe_customer_id' => $customerId]);

        $pmId = $this->createTestPaymentMethod();
        $this->stripeService->attachPaymentMethod($customerId, $pmId);

        $stripeSub = $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'default_payment_method' => $pmId,
        ]);

        $periodStart = now()->subDays(rand(5, 25))->startOfDay();
        $periodEnd = $periodStart->copy()->addMonth();

        Subscription::create([
            'child_id' => $child->id,
            'stripe_subscription_id' => $stripeSub->id,
            'stripe_price_id' => $priceId,
            'status' => SubscriptionStatus::Active,
            'override_price' => null,
            'overridden_by' => null,
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
        ]);
    }

    public function createPastDueSubscription(User $parent, Child $child, string $priceId): void
    {
        $customerId = $this->stripeService->createCustomer(
            $parent->email,
            $parent->getFilamentName()
        );

        $parent->update(['stripe_customer_id' => $customerId]);

        $pmId = $this->createTestPaymentMethod();
        $this->stripeService->attachPaymentMethod($customerId, $pmId);

        $stripeSub = $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'default_payment_method' => $pmId,
        ]);

        $periodStart = now()->subDays(rand(5, 25))->startOfDay();
        $periodEnd = $periodStart->copy()->addMonth();

        // Statut forcé en BDD pour la démo — past_due réel nécessiterait
        // un vrai cycle de renouvellement échoué via webhook Stripe
        Subscription::create([
            'child_id' => $child->id,
            'stripe_subscription_id' => $stripeSub->id,
            'stripe_price_id' => $priceId,
            'status' => SubscriptionStatus::PastDue,
            'override_price' => null,
            'overridden_by' => null,
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
        ]);
    }

    public function createCanceledSubscription(User $parent, Child $child, string $priceId): void
    {
        $customerId = $parent->stripe_customer_id
            ?? $this->stripeService->createCustomer($parent->email, $parent->getFilamentName());

        if (! $parent->stripe_customer_id) {
            $parent->update(['stripe_customer_id' => $customerId]);
        }

        $pmId = $this->createTestPaymentMethod();
        $this->stripeService->attachPaymentMethod($customerId, $pmId);

        $stripeSub = $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'default_payment_method' => $pmId,
        ]);

        $this->stripeService->cancelSubscription($stripeSub->id);

        $periodStart = now()->subDays(rand(5, 25))->startOfDay();
        $periodEnd = $periodStart->copy()->addMonth();

        Subscription::create([
            'child_id' => $child->id,
            'stripe_subscription_id' => $stripeSub->id,
            'stripe_price_id' => $priceId,
            'status' => SubscriptionStatus::Canceled,
            'override_price' => null,
            'overridden_by' => null,
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
            'canceled_at' => now(),
        ]);
    }

    public function createFreeSubscription(Child $child, User $admin): void
    {
        $periodStart = now()->subDays(rand(5, 25))->startOfDay();
        $periodEnd = $periodStart->copy()->addMonth();

        Subscription::create([
            'child_id' => $child->id,
            'stripe_subscription_id' => null,
            'stripe_price_id' => null,
            'status' => SubscriptionStatus::Free,
            'override_price' => 0.00,
            'overridden_by' => $admin->id,
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
        ]);
    }

    // -------------------------------------------------------------------------
    // Création du produit et de son tarif
    // -------------------------------------------------------------------------

    public function initProduct(): string
    {
        $product = $this->stripe->products->create([
            'name' => 'Abonnement Cartes Animées',
            'description' => 'Abonnement mensuel par enfant à l\'application Cartes Animées',
        ]);

        $this->writeEnv('STRIPE_PRODUCT_ID', $product->id);

        return $product->id;
    }

    public function initPrice(string $productId, float $amount, User $admin): string
    {
        $price = $this->stripe->prices->create([
            'unit_amount' => (int) ($amount * 100),
            'currency' => 'eur',
            'recurring' => ['interval' => 'month'],
            'product' => $productId,
            'nickname' => 'Commission variable pour les orthophonistes',
        ]);

        SubscriptionPriceHistory::create([
            'price' => $amount,
            'stripe_price_id' => $price->id,
            'effective_from' => now(),
            'created_by' => $admin->id,
        ]);

        return $price->id;
    }

    private function writeEnv(string $key, string $value): void
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        if (preg_match("/^{$key}=.*/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= PHP_EOL."{$key}={$value}";
        }

        file_put_contents($path, $content);
    }
}
