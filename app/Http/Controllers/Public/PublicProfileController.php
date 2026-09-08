<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\SocialLink;
use App\Models\CustomLink;
use App\Services\AnalyticsService;
use App\Services\ContactCardService;
use App\Jobs\TrackAnalytics;
use App\Constants\AnalyticsEvents;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected ContactCardService $contactCardService
    ) {}

    public function home()
    {
        return view('home.index');
    }

    public function show(string $slug, Request $request)
    {
        $reserved = ['admin', 'login', 'register', 'dashboard', 'api', 'pricing', 'support', 'about', 'contact', 'settings'];
        if (in_array(strtolower($slug), $reserved)) {
            abort(404);
        }

        $profile = QRProfile::where('slug', strtolower($slug))
            ->with(['socialLinks', 'customLinks', 'template', 'user'])
            ->first();

        if (!$profile) {
            return response()->view('errors.404_profile', [], 404);
        }

        if ($profile->status !== 'active') {
            return response()->view('profile.unavailable', ['profile' => $profile], 403);
        }

        // Record scan analytics (sync fallback + queue job dispatch)
        try {
            $this->analyticsService->recordScan($profile, $request);
        } catch (\Throwable $e) {
            // Log error silently to preserve high performance profile rendering
            \Illuminate\Support\Facades\Log::error("Analytics scan error on /p/{$slug}: " . $e->getMessage());
        }

        return view('profile.show', compact('profile'));
    }

    public function showBooking(string $slug)
    {
        $profile = QRProfile::where('slug', strtolower($slug))->firstOrFail();
        return view('profile.booking', compact('profile'));
    }

    public function downloadContact(string $slug, Request $request)
    {
        $profile = QRProfile::where('slug', strtolower($slug))->firstOrFail();

        if ($profile->status !== 'active') {
            abort(403, 'Profile is inactive');
        }

        try {
            $this->analyticsService->recordClick($profile, 0, $request, AnalyticsEvents::CONTACT_SAVE);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Analytics error on contact download: " . $e->getMessage());
        }

        $vcardContent = $this->contactCardService->generateVCard($profile);
        $fileName = str_replace(' ', '_', strtolower($profile->name)) . '.vcf';

        return response($vcardContent, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function trackClick(int $profileId, int $linkId, Request $request)
    {
        $profile = QRProfile::findOrFail($profileId);

        if ($profile->status !== 'active') {
            abort(403, 'Profile is inactive');
        }

        $targetUrl = null;
        $type = $request->query('type', 'custom');
        $eventType = AnalyticsEvents::LINK_CLICK;

        if ($type === 'social') {
            $link = SocialLink::where('profile_id', $profile->id)->findOrFail($linkId);
            $targetUrl = $link->url;
            $eventType = 'social_click';
        } else {
            $link = CustomLink::where('profile_id', $profile->id)->findOrFail($linkId);
            $targetUrl = $link->url;
            $eventType = AnalyticsEvents::LINK_CLICK;
        }

        // Validate target URL protocol to prevent open redirect vulnerabilities
        $targetUrl = trim($targetUrl);
        $scheme = parse_url($targetUrl, PHP_URL_SCHEME);
        if (!$scheme || !in_array(strtolower($scheme), ['http', 'https'])) {
            abort(400, 'Invalid or unsafe destination URL format');
        }

        // Record tracking event asynchronously
        try {
            $this->analyticsService->recordClick($profile, $linkId, $request, $eventType, ['platform' => $link->platform ?? 'custom']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Analytics click error: " . $e->getMessage());
        }

        return redirect()->away($targetUrl);
    }
}
