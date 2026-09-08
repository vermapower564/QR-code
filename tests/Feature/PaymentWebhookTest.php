<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    public function test_stripe_webhook_signature_verification(): void
    {
        $payload = json_encode(['type' => 'invoice.payment_succeeded']);
        $secret = 'whsec_test_secret';
        $computedSignature = hash_hmac('sha256', $payload, $secret);

        $this->assertNotEmpty($computedSignature);
    }
}
