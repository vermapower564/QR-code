<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function show(int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        $stats = $this->analyticsService->getProfileStats($profile);

        return view('dashboard.profiles.analytics', compact('profile', 'stats'));
    }
}
