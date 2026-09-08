<?php

namespace App\Services;

use App\Models\User;
use App\Models\QRProfile;

class FeatureService
{
    /**
     * Check if user's plan supports a specific feature key.
     */
    public function canUseFeature(User $user, string $featureKey): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $plan = $user->plan;
        if (!$plan) {
            return false;
        }

        return $plan->hasFeature($featureKey);
    }

    /**
     * Check if user is allowed to create another profile based on plan limit.
     */
    public function canCreateProfile(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $plan = $user->plan;
        if (!$plan) {
            // Free plan default limit = 1
            return $user->qrProfiles()->count() < 1;
        }

        if ($plan->profile_limit === -1) { // -1 means unlimited
            return true;
        }

        return $user->qrProfiles()->count() < $plan->profile_limit;
    }

    /**
     * Check if custom link limit for a profile has been reached.
     */
    public function canAddCustomLink(User $user, QRProfile $profile): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $plan = $user->plan;
        $limit = $plan ? $plan->link_limit : 5; // Default free limit = 5

        if ($limit === -1) {
            return true;
        }

        return $profile->customLinks()->count() < $limit;
    }
}
