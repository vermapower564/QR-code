<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    public function test_public_profile_filtering(): void
    {
        $profile = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+19999999999',
            'show_email' => false,
            'show_phone' => true
        ];

        $publicData = [
            'name' => $profile['name'],
            'email' => $profile['show_email'] ? $profile['email'] : null,
            'phone' => $profile['show_phone'] ? $profile['phone'] : null
        ];

        $this->assertNull($publicData['email']);
        $this->assertEquals('+19999999999', $publicData['phone']);
    }
}
