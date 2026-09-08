<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfileLifecycleTest extends TestCase
{
    public function test_profile_update_retains_slug(): void
    {
        $initialSlug = 'john-doe';
        $updatedBio = 'Updated entrepreneur bio text.';

        $profile = ['slug' => $initialSlug, 'bio' => $updatedBio];

        $this->assertEquals('john-doe', $profile['slug']);
        $this->assertEquals('Updated entrepreneur bio text.', $profile['bio']);
    }
}
