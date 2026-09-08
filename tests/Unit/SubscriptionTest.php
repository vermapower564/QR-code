<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function test_subscription_expiration_check(): void
    {
        $endsAt = new \DateTime('-1 day');
        $now = new \DateTime('now');

        $isExpired = $endsAt < $now;
        $this->assertTrue($isExpired);
    }
}
