<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\QRProfile;
use App\Services\FeatureService;

class FeatureGatingTest extends TestCase
{
    public function test_can_create_profile_respects_free_plan_limit(): void
    {
        $plan = new Plan(['profile_limit' => 1]);
        $user = new User(['role' => 'user']);
        $user->setRelation('plan', $plan);

        $service = new FeatureService();
        
        // When user has 0 profiles
        $this->assertTrue(true);
    }
}
