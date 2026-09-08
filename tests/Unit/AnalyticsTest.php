<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AnalyticsTest extends TestCase
{
    public function test_ip_hashing_anonymization(): void
    {
        $ip = '192.168.1.100';
        $salt = 'app_secret_salt_2026';
        $hash1 = hash('sha256', $ip . $salt);
        $hash2 = hash('sha256', $ip . $salt);

        $this->assertEquals($hash1, $hash2);
        $this->assertNotEquals($ip, $hash1);
    }

    public function test_ctr_calculation(): void
    {
        $scans = 100;
        $clicks = 25;
        $ctr = ($scans > 0) ? round(($clicks / $scans) * 100, 2) : 0;

        $this->assertEquals(25.0, $ctr);
    }
}
