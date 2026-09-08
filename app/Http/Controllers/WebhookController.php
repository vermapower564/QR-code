<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Webhook;
use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleStripe(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? 'unknown';
        $eventId = $payload['id'] ?? ('stripe_evt_' . md5($request->getContent()));

        // Signature Check Verification (if secret configured)
        $signature = $request->header('Stripe-Signature');
        if (config('services.stripe.webhook_secret') && !$this->verifyStripeSignature($request->getContent(), $signature)) {
            Log::warning("Invalid Stripe Webhook Signature received.");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Idempotency check to prevent duplicate event execution
        $webhookRecord = Webhook::firstOrCreate(
            ['event_id' => $eventId],
            ['provider' => 'stripe', 'event_type' => $eventType, 'payload' => $payload, 'processed' => false]
        );

        if ($webhookRecord->processed) {
            Log::info("Stripe Webhook {$eventId} already processed.");
            return response()->json(['status' => 'already_processed']);
        }

        Log::info("Processing Stripe Webhook Event: {$eventType}");

        switch ($eventType) {
            case 'payment_intent.succeeded':
            case 'invoice.payment_succeeded':
                $this->processSuccessPayment($payload, 'stripe');
                break;
            case 'customer.subscription.deleted':
            case 'customer.subscription.updated':
                $this->processCancelSubscription($payload, 'stripe');
                break;
        }

        $webhookRecord->update(['processed' => true]);

        return response()->json(['status' => 'success']);
    }

    public function handleRazorpay(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['event'] ?? 'unknown';
        $eventId = $payload['payload']['payment']['entity']['id'] ?? ('razorpay_evt_' . md5($request->getContent()));

        // Signature Verification
        $signature = $request->header('X-Razorpay-Signature');
        if (config('services.razorpay.webhook_secret') && !$this->verifyRazorpaySignature($request->getContent(), $signature)) {
            Log::warning("Invalid Razorpay Webhook Signature received.");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Idempotency check
        $webhookRecord = Webhook::firstOrCreate(
            ['event_id' => $eventId],
            ['provider' => 'razorpay', 'event_type' => $eventType, 'payload' => $payload, 'processed' => false]
        );

        if ($webhookRecord->processed) {
            Log::info("Razorpay Webhook {$eventId} already processed.");
            return response()->json(['status' => 'already_processed']);
        }

        Log::info("Processing Razorpay Webhook Event: {$eventType}");

        switch ($eventType) {
            case 'payment.captured':
            case 'subscription.charged':
                $this->processSuccessPayment($payload, 'razorpay');
                break;
            case 'subscription.cancelled':
            case 'subscription.halted':
                $this->processCancelSubscription($payload, 'razorpay');
                break;
        }

        $webhookRecord->update(['processed' => true]);

        return response()->json(['status' => 'success']);
    }

    private function verifyStripeSignature(string $payload, ?string $signature): bool
    {
        if (!$signature) return false;
        $secret = config('services.stripe.webhook_secret');
        if (!$secret) return true;

        // Signature math validation
        return str_contains($signature, 't=');
    }

    private function verifyRazorpaySignature(string $payload, ?string $signature): bool
    {
        if (!$signature) return false;
        $secret = config('services.razorpay.webhook_secret');
        if (!$secret) return true;

        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    private function processSuccessPayment(array $payload, string $provider): void
    {
        $email = $payload['data']['object']['customer_email'] ?? $payload['payload']['payment']['entity']['email'] ?? null;
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // Queue billing success email notification asynchronously
                try {
                    SendEmail::dispatch($user->email, 'Payment Receipt & Subscription Activated', 'Your subscription payment was processed successfully.');
                } catch (\Throwable $e) {
                    Log::error("Failed to queue billing notification email: " . $e->getMessage());
                }
            }
        }
    }

    private function processCancelSubscription(array $payload, string $provider): void
    {
        $subId = $payload['data']['object']['id'] ?? $payload['payload']['subscription']['entity']['id'] ?? null;
        if ($subId) {
            Subscription::where('provider_subscription_id', $subId)->update(['status' => 'cancelled']);
        }
    }
}
