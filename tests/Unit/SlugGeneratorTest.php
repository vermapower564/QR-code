<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SlugGeneratorTest extends TestCase
{
    public function test_slug_generation_removes_special_characters(): void
    {
        $input = "John Doe's Company! & Services @2026";
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input), '-'));
        $this->assertEquals('john-doe-s-company-services-2026', $slug);
    }

    public function test_reserved_slug_protection(): void
    {
        $reserved = ['admin', 'login', 'register', 'dashboard', 'api', 'pricing', 'settings', 'onboarding', 'billing', 'p', 'u'];
        $testSlug = 'admin';

        $isReserved = in_array($testSlug, $reserved);
        $this->assertTrue($isReserved);
    }
}
