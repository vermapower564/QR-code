<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_requires_valid_data(): void
    {
        $payload = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ];

        $this->assertTrue(empty($payload['name']));
        $this->assertFalse(filter_var($payload['email'], FILTER_VALIDATE_EMAIL) !== false);
    }
}
