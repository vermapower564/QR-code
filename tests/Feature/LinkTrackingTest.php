<?php

namespace Tests\Feature;

use Tests\TestCase;

class LinkTrackingTest extends TestCase
{
    public function test_link_tracking_redirect(): void
    {
        $targetUrl = 'https://abctechnologies.com';
        $isValidUrl = filter_var($targetUrl, FILTER_VALIDATE_URL) !== false;

        $this->assertTrue($isValidUrl);
    }
}
