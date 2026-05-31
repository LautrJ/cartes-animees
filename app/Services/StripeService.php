<?php

namespace App\Services;

use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\Webhook;

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
            'currency' => 'eur',
            'recurring' => ['interval' => 'month'],
            'product' => config('services.stripe.product_id'),
            'nickname' => 'Commission variable pour les orthophonistes',
        ]);

        return $price->id;
    }

    public function createCustomer(string $email, string $name): string
    {
        $customer = $this->stripe->customers->create([
            'email' => $email,
            'name' => $name,
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

    public function createSubscription(string $customerId, string $priceId): Subscription
    {
        return $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'billing_mode' => ['type' => 'classic'],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
        ]);
    }

    public function cancelSubscription(string $subscriptionId): void
    {
        $this->stripe->subscriptions->cancel($subscriptionId);
    }

    public function createCoupon(float $amountOff): string
    {
        $coupon = $this->stripe->coupons->create([
            'amount_off' => (int) ($amountOff * 100),
            'currency'   => 'eur',
            'duration'   => 'forever',
            'name'       => 'Réduction appliquée par l\'administrateur',
        ]);

        return $coupon->id;
    }

    public function applyCouponToSubscription(string $subscriptionId, string $couponId): void
    {
        $this->stripe->subscriptions->update($subscriptionId, [
            'discounts' => [['coupon' => $couponId]],
        ]);
    }

    public function removeCouponFromSubscription(string $subscriptionId): void
    {
        $this->stripe->subscriptions->update($subscriptionId, [
            'discounts' => [],
        ]);
    }

    public function constructWebhookEvent(string $payload, string $signature): Event
    {
        return Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    }
}
