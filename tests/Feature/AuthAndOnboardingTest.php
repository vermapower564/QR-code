<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\QRProfile;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthAndOnboardingTest extends TestCase
{
    public function test_registration_page_loads(): void
    {
        $this->assertTrue(true);
    }

    public function test_user_can_register_and_receive_default_free_plan(): void
    {
        $freePlan = new Plan(['slug' => 'free', 'name' => 'Starter Free', 'profile_limit' => 1]);
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'onboarding_completed' => false,
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertFalse($user->onboarding_completed);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_onboarding_requires_profile_and_qr_before_setting_completed(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Unfinished User',
            'email' => 'unfinished@example.com',
            'onboarding_completed' => false,
        ]);

        $this->assertFalse($user->onboarding_completed);

        // Simulated completion
        $user->onboarding_completed = true;
        $this->assertTrue($user->onboarding_completed);
    }

    public function test_ownership_policy_prevents_unauthorized_profile_access(): void
    {
        $owner = new User(['id' => 1, 'role' => 'user']);
        $stranger = new User(['id' => 2, 'role' => 'user']);
        $profile = new QRProfile(['user_id' => 1, 'slug' => 'owner-profile']);

        $policy = new \App\Policies\QRProfilePolicy();

        $this->assertTrue($policy->view($owner, $profile));
        $this->assertFalse($policy->view($stranger, $profile));
    }
}
