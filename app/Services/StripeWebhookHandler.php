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
            // Events attendus mais non traités explicitement
            'invoice.paid',
            'invoice.payment_action_required',
            'invoice_payment.paid',
            'payment_intent.requires_action',
            'payment_intent.succeeded',
            'payment_intent.created',
            'charge.succeeded',
            'charge.updated',
            'customer.updated',
            'payment_method.attached' => null,
            default => Log::channel('stripe')->info('Event Stripe non géré : '.$event->type),
        };
    }

    private function handleInvoicePaymentSucceeded(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscriptionId = $stripeInvoice->parent?->subscription_details?->subscription
            ?? $stripeInvoice->subscription
            ?? null;

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

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

        // attempt_count = 0 means 3DS is pending, not a genuine failure — skip
        if (($stripeInvoice->attempt_count ?? 0) === 0) {
            Log::channel('stripe')->info('Tentative en attente d\'action 3DS, pas un échec réel', [
                'stripe_invoice_id' => $stripeInvoice->id,
            ]);

            return;
        }

        $subscriptionId = $stripeInvoice->parent?->subscription_details?->subscription
            ?? $stripeInvoice->subscription
            ?? null;

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            Log::channel('stripe')->warning('Subscription introuvable pour invoice.payment_failed', [
                'stripe_subscription_id' => $subscriptionId,
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

        // Un échec sur la toute première facture (création de l'abonnement, 3DS refusé/annulé)
        // ne doit pas passer l'abonnement en "past_due" — ce statut implique une période active
        // précédente. Il doit rester "incomplete" pour permettre une nouvelle tentative de paiement.
        // La notification "paiement échoué" (qui parle de renouvellement et de suspension d'accès)
        // n'a également de sens que pour un vrai échec de renouvellement.
        $isFirstInvoice = $stripeInvoice->billing_reason === 'subscription_create';

        if ($isFirstInvoice) {
            $subscription->update(['status' => SubscriptionStatus::Incomplete]);
        } else {
            $subscription->update(['status' => SubscriptionStatus::PastDue]);

            $subscription->child->parent->notify(new PaymentFailedNotification(
                childFirstName: $subscription->child->first_name,
            ));
        }

        Log::channel('stripe')->warning('Paiement échoué', [
            'stripe_invoice_id' => $stripeInvoice->id,
            'subscription_id' => $subscription->id,
            'billing_reason' => $stripeInvoice->billing_reason,
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
            'incomplete' => SubscriptionStatus::Incomplete,
            'past_due' => SubscriptionStatus::PastDue,
            'canceled' => SubscriptionStatus::Canceled,
            default => null,
        };

        $data = array_filter([
            'current_period_start' => $stripeSubscription->current_period_start
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                : null,
            'current_period_end' => $stripeSubscription->current_period_end
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null,
        ]);

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
