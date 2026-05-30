<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\PaymentSucceededNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Event;

class StripeWebhookHandler
{
    public function handle(Event $event): void
    {
        match ($event->type) {
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($event),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
            'invoice.finalized' => $this->handleInvoiceFinalized($event),
            default => Log::channel('stripe')->info('Event Stripe non géré : '.$event->type),
        };
    }

    private function handleInvoicePaymentSucceeded(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

        if (! $subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour invoice.payment_succeeded', [
                'stripe_subscription_id' => $stripeInvoice->subscription,
            ]);

            return;
        }

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'subscription_id' => $subscription->id,
                'amount' => $stripeInvoice->amount_paid / 100,
                'status' => InvoiceStatus::Paid,
                'invoice_pdf' => $stripeInvoice->invoice_pdf,
                'period_start' => now()->createFromTimestamp($stripeInvoice->period_start),
                'period_end' => now()->createFromTimestamp($stripeInvoice->period_end),
                'paid_at' => now(),
            ]
        );

        $subscription->update([
            'status' => SubscriptionStatus::Active,
            'current_period_start' => Carbon::createFromTimestamp($stripeInvoice->period_start),
            'current_period_end' => Carbon::createFromTimestamp($stripeInvoice->period_end),
        ]);

        $subscription->child->parent->notify(new PaymentSucceededNotification(
            childFirstName: $subscription->child->first_name,
            amount: $stripeInvoice->amount_paid / 100,
        ));

        Log::channel('stripe')->info('Facture payée', [
            'stripe_invoice_id' => $stripeInvoice->id,
            'amount' => $stripeInvoice->amount_paid / 100,
        ]);
    }

    private function handleInvoicePaymentFailed(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

        if (! $subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour invoice.payment_failed', [
                'stripe_subscription_id' => $stripeInvoice->subscription,
            ]);

            return;
        }

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'subscription_id' => $subscription->id,
                'amount' => $stripeInvoice->amount_due / 100,
                'status' => InvoiceStatus::Open,
                'period_start' => now()->createFromTimestamp($stripeInvoice->period_start),
                'period_end' => now()->createFromTimestamp($stripeInvoice->period_end),
                'paid_at' => null,
            ]
        );

        $subscription->update(['status' => SubscriptionStatus::PastDue]);

        $subscription->child->parent->notify(new PaymentFailedNotification(
            childFirstName: $subscription->child->first_name,
        ));

        Log::channel('stripe')->warning('Paiement échoué', [
            'stripe_invoice_id' => $stripeInvoice->id,
            'subscription_id' => $subscription->id,
        ]);
    }

    private function handleSubscriptionDeleted(Event $event): void
    {
        $stripeSubscription = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (! $subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour customer.subscription.deleted', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);

            return;
        }

        $subscription->update([
            'status' => SubscriptionStatus::Canceled,
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

        if (! $subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour customer.subscription.updated', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);

            return;
        }

        $stripeStatus = match ($stripeSubscription->status) {
            'active' => SubscriptionStatus::Active,
            'past_due' => SubscriptionStatus::PastDue,
            'canceled' => SubscriptionStatus::Canceled,
            default => null,
        };

        $data = [
            'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
        ];

        if ($stripeStatus) {
            $data['status'] = $stripeStatus;
        }

        $subscription->update($data);

        Log::channel('stripe')->info('Abonnement mis à jour', [
            'stripe_subscription_id' => $stripeSubscription->id,
        ]);
    }

    private function handleInvoiceFinalized(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->parent?->subscription_details?->subscription)->first();

        if (! $subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour invoice.finalized', [
                'stripe_invoice_id' => $stripeInvoice->id,
            ]);

            return;
        }

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'subscription_id' => $subscription->id,
                'amount' => $stripeInvoice->amount_due / 100,
                'status' => InvoiceStatus::Open,
                'invoice_pdf' => $stripeInvoice->invoice_pdf,
                'period_start' => now()->createFromTimestamp($stripeInvoice->period_start),
                'period_end' => now()->createFromTimestamp($stripeInvoice->period_end),
                'paid_at' => null,
            ]
        );

        Log::channel('stripe')->info('Facture finalisée', [
            'stripe_invoice_id' => $stripeInvoice->id,
        ]);
    }
}
