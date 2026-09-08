<?php

namespace App\Services;

use App\Models\User;
use App\Models\QRProfile;
use Illuminate\Support\Facades\Cache;

class FeatureService
{
    /**
     * Check if user's active plan supports a specific feature key.
     */
    public function canUseFeature(User $user, string $featureKey): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $cacheKey = "user_feature_{$user->id}_{$featureKey}";

        return Cache::remember($cacheKey, 300, function () use ($user, $featureKey) {
            $plan = $user->plan;
            if (!$plan) {
                // Default free plan fallback features
                $freeFeatures = ['basic_templates', 'contact_card'];
                return in_array($featureKey, $freeFeatures);
            }

            $value = $plan->getFeatureValue($featureKey);
            if ($value === null) {
                // Default checks for standard features
                $enabledByPlan = [
                    'pro' => ['custom_qr', 'qr_logo', 'advanced_analytics', 'remove_branding', 'custom_links', 'contact_card', 'qr_download', 'premium_templates'],
                    'business' => ['custom_qr', 'qr_logo', 'advanced_analytics', 'remove_branding', 'custom_links', 'contact_card', 'qr_download', 'premium_templates', 'team_members', 'bulk_qr', 'api_access', 'custom_domain', 'priority_support'],
                ];
                $planSlug = strtolower($plan->slug);
                return isset($enabledByPlan[$planSlug]) && in_array($featureKey, $enabledByPlan[$planSlug]);
            }

            return $value === '1' || $value === 'true' || $value === true || strtolower((string)$value) === 'unlimited';
        });
    }

    /**
     * Get raw value of a feature key for user's plan.
     */
    public function getFeatureValue(User $user, string $featureKey, $default = null)
    {
        if ($user->isAdmin()) {
            return 'unlimited';
        }

        $plan = $user->plan;
        if (!$plan) {
            return $default;
        }

        return $plan->getFeatureValue($featureKey, $default);
    }

    /**
     * Check if user is allowed to create another profile based on plan limit.
     */
    public function canCreateProfile(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $limit = $this->getProfileLimit($user);

        if ($limit === -1 || $limit === 'unlimited') {
            return true;
        }

        return $user->qrProfiles()->count() < (int)$limit;
    }

    /**
     * Get maximum profile limit for user (-1 for unlimited).
     */
    public function getProfileLimit(User $user): int
    {
        if ($user->isAdmin()) {
            return -1;
        }

        $plan = $user->plan;
        if (!$plan) {
            return 1; // Default free limit
        }

        return (int) $plan->profile_limit;
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
        $limit = $plan ? (int)$plan->link_limit : 5; // Default free limit = 5

        if ($limit === -1 || $limit === 'unlimited') {
            return true;
        }

        return $profile->customLinks()->count() < $limit;
    }

    /**
     * Helper methods for explicit feature checks.
     */
    public function canUseAdvancedAnalytics(User $user): bool
    {
        return $this->canUseFeature($user, 'advanced_analytics');
    }

    public function canUseCustomQR(User $user): bool
    {
        return $this->canUseFeature($user, 'custom_qr');
    }

    public function canRemoveBranding(User $user): bool
    {
        return $this->canUseFeature($user, 'remove_branding');
    }

    public function canUseBulkQR(User $user): bool
    {
        return $this->canUseFeature($user, 'bulk_qr');
    }

    public function canUseAPI(User $user): bool
    {
        return $this->canUseFeature($user, 'api_access');
    }

    public function canAddTeamMember(User $user): bool
    {
        return $this->canUseFeature($user, 'team_members');
    }

    /**
     * Invalidate user feature cache.
     */
    public function clearCache(User $user): void
    {
        Cache::forget("user_feature_{$user->id}_custom_qr");
        Cache::forget("user_feature_{$user->id}_advanced_analytics");
        Cache::forget("user_feature_{$user->id}_remove_branding");
        Cache::forget("user_feature_{$user->id}_bulk_qr");
        Cache::forget("user_feature_{$user->id}_api_access");
        Cache::forget("user_feature_{$user->id}_team_members");
    }
}
