<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PlanLimitsTest.php extends TestCase
{
    public function test_free_plan_profile_limit(): void
    {
        $freePlanMaxProfiles = 1;
        $userProfilesCount = 1;

        $canCreateProfile = $userProfilesCount < $freePlanMaxProfiles;
        $this->assertFalse($canCanCreateProfile ?? $canCreateProfile);
    }

    public function test_pro_plan_profile_limit(): void
    {
        $proPlanMaxProfiles = 10;
        $userProfilesCount = 2;

        $canCreateProfile = $userProfilesCount < $proPlanMaxProfiles;
        $this->assertTrue($canCreateProfile);
    }
}
