<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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

    public function retrievePaymentIntent(string $id): \Stripe\PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($id);
    }

    public function retrieveInvoice(string $id): \Stripe\Invoice
    {
        return $this->stripe->invoices->retrieve($id, [
            'expand' => ['payment_intent'],
        ]);
    }

    public function getClientSecretForSubscription(string $subscriptionId, string $customerId): ?string
    {
        // API 2026-04-22 (dahlia): `payment_intent` was removed from the Invoice object,
        // and the `invoice` field is no longer returned on PaymentIntents in list responses.
        // The only reliable approach is to list the customer's PaymentIntents and pick the
        // first one in a confirmable status. In practice, at most one PI is pending per customer.
        $pendingStatuses = ['requires_payment_method', 'requires_confirmation', 'requires_action'];

        $sub       = $this->stripe->subscriptions->retrieve($subscriptionId, ['expand' => ['latest_invoice']]);
        $invoice   = $sub->latest_invoice;
        $invoiceId = is_string($invoice) ? $invoice : ($invoice->id ?? null);

        Log::channel('stripe')->info('[3DS] looking for PI', [
            'subscription_id' => $subscriptionId,
            'invoice_id'      => $invoiceId,
        ]);

        try {
            $pis = $this->stripe->paymentIntents->all([
                'customer' => $customerId,
                'limit'    => 10,
            ]);

            foreach ($pis->data as $pi) {
                if (in_array($pi->status, $pendingStatuses, true)) {
                    Log::channel('stripe')->info('[3DS] client_secret found', [
                        'pi_id'  => $pi->id,
                        'status' => $pi->status,
                    ]);

                    return $pi->client_secret;
                }
            }
        } catch (\Throwable $e) {
            Log::channel('stripe')->warning('[3DS] paymentIntents list failed', [
                'error' => $e->getMessage(),
            ]);
        }

        Log::channel('stripe')->error('[3DS] client_secret not found', [
            'subscription_id' => $subscriptionId,
            'invoice_id'      => $invoiceId,
        ]);

        return null;
    }

    public function retrieveSubscription(string $subscriptionId): Subscription
    {
        return $this->stripe->subscriptions->retrieve($subscriptionId);
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
