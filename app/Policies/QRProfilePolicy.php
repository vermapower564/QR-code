<?php

namespace App\Policies;

use App\Models\QRProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QRProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the profile.
     */
    public function view(User $user, QRProfile $qrProfile): bool
    {
        return $user->isAdmin() || $user->id === $qrProfile->user_id;
    }

    /**
     * Determine whether the user can create profiles.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the profile.
     */
    public function update(User $user, QRProfile $qrProfile): bool
    {
        return $user->isAdmin() || $user->id === $qrProfile->user_id;
    }

    /**
     * Determine whether the user can delete the profile.
     */
    public function delete(User $user, QRProfile $qrProfile): bool
    {
        return $user->isAdmin() || $user->id === $qrProfile->user_id;
    }

    /**
     * Determine whether the user can manage QR styling and downloads.
     */
    public function manageQr(User $user, QRProfile $qrProfile): bool
    {
        return $user->isAdmin() || $user->id === $qrProfile->user_id;
    }

    /**
     * Determine whether the user can view profile analytics.
     */
    public function viewAnalytics(User $user, QRProfile $qrProfile): bool
    {
        return $user->isAdmin() || $user->id === $qrProfile->user_id;
    }
}
