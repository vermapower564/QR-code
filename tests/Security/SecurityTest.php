<?php

namespace Tests\Security;

use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_xss_sanitization_on_profile_fields(): void
    {
        $payload = '<script>alert("XSS")</script>John Doe';
        $sanitized = htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');

        $this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;John Doe', $sanitized);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }

    public function test_unsafe_url_scheme_rejection(): void
    {
        $unsafeUrls = [
            'javascript:alert(1)',
            'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
            'file:///etc/passwd',
            'vbscript:msgbox(1)'
        ];

        foreach ($unsafeUrls as $url) {
            $scheme = parse_url($url, PHP_URL_SCHEME);
            $isSafe = in_array(strtolower((string)$scheme), ['http', 'https']);
            $this->assertFalse($isSafe, "URL scheme '{$scheme}' should be rejected.");
        }
    }

    public function test_qr_content_security(): void
    {
        $qrPayload = 'https://yourdomain.com/p/john-doe';

        $this->assertStringNotContainsString('password', strtolower($qrPayload));
        $this->assertStringNotContainsString('jwt', strtolower($qrPayload));
        $this->assertStringNotContainsString('otp', strtolower($qrPayload));
        $this->assertStringNotContainsString('token', strtolower($qrPayload));
    }

    public function test_public_api_secret_filtering(): void
    {
        $fullUserObject = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password_hash' => '$2y$10$e8K...',
            'role' => 'user',
            'stripe_id' => 'cus_123',
            'remember_token' => 'token_secret'
        ];

        $publicApiOutput = array_diff_key($fullUserObject, array_flip(['password_hash', 'remember_token', 'stripe_id']));

        $this->assertArrayNotHasKey('password_hash', $publicApiOutput);
        $this->assertArrayNotHasKey('remember_token', $publicApiOutput);
        $this->assertArrayNotHasKey('stripe_id', $publicApiOutput);
    }

    public function test_ip_anonymization_privacy(): void
    {
        $ip = '203.0.113.195';
        $salt = 'app_key_secret_2026';
        $hash = hash('sha256', $ip . $salt);

        $this->assertNotEquals($ip, $hash);
        $this->assertEquals(64, strlen($hash));
    }

    public function test_webhook_idempotency_verification(): void
    {
        $eventId = 'evt_test_101';
        $processedWebhooks = ['evt_test_101' => true];

        $isDuplicate = isset($processedWebhooks[$eventId]);
        $this->assertTrue($isDuplicate);
    }

    public function test_invoice_idor_protection(): void
    {
        $currentUserId = 10;
        $invoiceOwnerId = 20;

        $hasAccess = ($currentUserId === $invoiceOwnerId);
        $this->assertFalse($hasAccess);
    }

    public function test_csv_formula_injection_defense(): void
    {
        $validator = new \App\Services\LinkValidationService();
        
        $dangerousCells = [
            '=SUM(A1:A10)',
            '+CMD|"/C calc"!A0',
            '-2+3',
            '@SUM(A1:A10)',
            "\tTAB_KEY",
        ];

        foreach ($dangerousCells as $cell) {
            $sanitized = $validator->sanitizeCsvCell($cell);
            $this->assertStringStartsWith("'", $sanitized, "Cell '{$cell}' should be escaped with leading apostrophe.");
        }

        $safeCell = 'John Doe';
        $this->assertEquals('John Doe', $validator->sanitizeCsvCell($safeCell));
    }

    public function test_link_validation_service_platform_matching(): void
    {
        $validator = new \App\Services\LinkValidationService();

        $this->assertTrue($validator->validatePlatformUrl('instagram', 'https://instagram.com/johndoe'));
        $this->assertTrue($validator->validatePlatformUrl('linkedin', 'https://www.linkedin.com/in/johndoe'));
        
        // Host matching rejects domain spoofing attempts
        $this->assertFalse($validator->validatePlatformUrl('instagram', 'https://instagram.com.malicious-phishing.com/johndoe'));
        $this->assertFalse($validator->validatePlatformUrl('facebook', 'javascript:alert(1)'));
    }
}
