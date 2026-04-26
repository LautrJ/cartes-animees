<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPrice(float $amount): string
    {
        $price = $this->stripe->prices->create([
            'unit_amount' => (int) ($amount * 100),
            'currency'    => 'eur',
            'recurring'   => ['interval' => 'month'],
            'product'     => config('services.stripe.product_id'),
            'nickname'    => 'Commission variable pour les orthophonistes',
        ]);

        return $price->id;
    }

    public function createCustomer(string $email, string $name): string
    {
        $customer = $this->stripe->customers->create([
            'email' => $email,
            'name'  => $name,
        ]);

        return $customer->id;
    }

    public function attachPaymentMethod(string $customerId, string $paymentMethodId): void
    {
        $this->stripe->paymentMethods->attach($paymentMethodId, [
            'customer' => $customerId,
        ]);

        $this->stripe->customers->update($customerId, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ]);
    }

    public function createSubscription(string $customerId, string $priceId): \Stripe\Subscription
    {
        return $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items'    => [['price' => $priceId]],
            'payment_behavior' => 'default_incomplete',
            'expand'   => ['latest_invoice.payment_intent'],
        ]);
    }

    public function cancelSubscription(string $subscriptionId): void
    {
        $this->stripe->subscriptions->cancel($subscriptionId);
    }

    public function constructWebhookEvent(string $payload, string $signature): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    }
}
