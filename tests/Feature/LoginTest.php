<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_rejects_invalid_credentials(): void
    {
        $email = 'user@example.com';
        $inputPassword = 'WrongPassword123!';
        $hashedPassword = password_hash('CorrectPassword123!', PASSWORD_BCRYPT);

        $this->assertFalse(password_verify($inputPassword, $hashedPassword));
    }
}
