<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Stripe\Event;

class StripeWebhookHandler
{
    public function handle(Event $event): void
    {
        match ($event->type) {
            'invoice.payment_succeeded'        => $this->handleInvoicePaymentSucceeded($event),
            'invoice.payment_failed'           => $this->handleInvoicePaymentFailed($event),
            'customer.subscription.deleted'    => $this->handleSubscriptionDeleted($event),
            'customer.subscription.updated'    => $this->handleSubscriptionUpdated($event),
            default => Log::channel('stripe')->info('Event Stripe non géré : ' . $event->type),
        };
    }

    private function handleInvoicePaymentSucceeded(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

        if (!$subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour invoice.payment_succeeded', [
                'stripe_subscription_id' => $stripeInvoice->subscription,
            ]);
            return;
        }

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'subscription_id' => $subscription->id,
                'amount'          => $stripeInvoice->amount_paid / 100,
                'status'          => InvoiceStatus::Paid,
                'period_start'    => now()->createFromTimestamp($stripeInvoice->period_start),
                'period_end'      => now()->createFromTimestamp($stripeInvoice->period_end),
                'paid_at'         => now(),
            ]
        );

        $subscription->update(['status' => SubscriptionStatus::Active]);

        Log::channel('stripe')->info('Facture payée', [
            'stripe_invoice_id' => $stripeInvoice->id,
            'amount'            => $stripeInvoice->amount_paid / 100,
        ]);
    }

    private function handleInvoicePaymentFailed(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

        if (!$subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour invoice.payment_failed', [
                'stripe_subscription_id' => $stripeInvoice->subscription,
            ]);
            return;
        }

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'subscription_id' => $subscription->id,
                'amount'          => $stripeInvoice->amount_due / 100,
                'status'          => InvoiceStatus::Open,
                'period_start'    => now()->createFromTimestamp($stripeInvoice->period_start),
                'period_end'      => now()->createFromTimestamp($stripeInvoice->period_end),
                'paid_at'         => null,
            ]
        );

        $subscription->update(['status' => SubscriptionStatus::PastDue]);

        Log::channel('stripe')->warning('Paiement échoué', [
            'stripe_invoice_id' => $stripeInvoice->id,
            'subscription_id'   => $subscription->id,
        ]);
    }

    private function handleSubscriptionDeleted(Event $event): void
    {
        $stripeSubscription = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour customer.subscription.deleted', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
            return;
        }

        $subscription->update([
            'status'      => SubscriptionStatus::Canceled,
            'canceled_at' => now(),
        ]);

        Log::channel('stripe')->info('Abonnement annulé', [
            'stripe_subscription_id' => $stripeSubscription->id,
        ]);
    }

    private function handleSubscriptionUpdated(Event $event): void
    {
        $stripeSubscription = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour customer.subscription.updated', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
            return;
        }

        $subscription->update([
            'current_period_start' => now()->createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end'   => now()->createFromTimestamp($stripeSubscription->current_period_end),
        ]);

        Log::channel('stripe')->info('Abonnement mis à jour', [
            'stripe_subscription_id' => $stripeSubscription->id,
        ]);
    }
}
