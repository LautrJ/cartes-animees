<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Services\StripeWebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = (new StripeService)->constructWebhookEvent($payload, $signature);
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature invalide', [
                'error' => $e->getMessage(),
            ]);

            return response('Signature invalide.', 400);
        }

        Log::channel('stripe')->info('Stripe event reçu', [
            'type' => $event->type,
            'id' => $event->id,
            'created' => $event->created,
            'data' => $event->data->toArray(),
        ]);

        try {
            (new StripeWebhookHandler)->handle($event);
        } catch (\Exception $e) {
            Log::channel('stripe')->error('Erreur traitement webhook Stripe', [
                'type' => $event->type,
                'id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return response('Erreur traitement.', 500);
        }

        return response('OK', 200);
    }
}
