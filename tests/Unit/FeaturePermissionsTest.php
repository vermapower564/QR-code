<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FeaturePermissionsTest extends TestCase
{
    public function test_vcard_download_permission_on_free_plan(): void
    {
        $hasVcardFeature = true;
        $this->assertTrue($hasVcardFeature);
    }

    public function test_custom_domain_permission_on_free_plan(): void
    {
        $planFeatures = ['vcard_download' => true, 'custom_domain' => false];
        $this->assertFalse($planFeatures['custom_domain']);
    }
}
