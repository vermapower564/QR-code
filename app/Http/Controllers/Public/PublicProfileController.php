<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\ProfileSlugHistory;
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
        $slugLower = strtolower(trim($slug));

        $reserved = config('reserved_slugs', [
            'admin', 'login', 'register', 'dashboard', 'api', 'pricing', 'support',
            'about', 'contact', 'settings', 'billing', 'profiles', 'analytics', 'templates', 'domains', 'p', 'u'
        ]);

        if (in_array($slugLower, $reserved)) {
            abort(404);
        }

        // High Performance Redis/File Cache (60 minutes)
        $cacheKey = 'profile:' . $slugLower;
        $profile = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($slugLower) {
            return QRProfile::where('slug', $slugLower)
                ->with(['socialLinks', 'customLinks', 'template', 'user'])
                ->first();
        });

        // Secondary lookup: Historical slug redirect (301 Permanent Redirect)
        if (!$profile) {
            $history = ProfileSlugHistory::where('old_slug', $slugLower)->first();
            if ($history) {
                return redirect()->route('profile.show', ['slug' => $history->new_slug], 301);
            }
            return response()->view('errors.404_profile', [], 404);
        }

        // Check profile status and user account status
        if ($profile->status !== 'active' || ($profile->user && $profile->user->status === 'suspended')) {
            return response()->view('profile.unavailable', ['profile' => $profile], 403);
        }

        if ($profile->profile_password) {
            $sessionKey = 'profile_unlocked_' . $profile->id;
            if (!$request->session()->has($sessionKey)) {
                return view('profile.password', compact('profile'));
            }
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

    public function unlock(string $slug, Request $request)
    {
        $profile = QRProfile::where('slug', strtolower($slug))->firstOrFail();
        
        $request->validate(['password' => 'required|string']);

        if ($request->password === $profile->profile_password) {
            $request->session()->put('profile_unlocked_' . $profile->id, true);
            return redirect()->route('profile.show', $profile->slug);
        }

        return back()->withErrors(['password' => 'Incorrect password']);
    }

    public function submitLead(string $slug, Request $request)
    {
        $profile = QRProfile::where('slug', strtolower($slug))->firstOrFail();

        if (!$profile->enable_lead_capture) {
            abort(403, 'Lead capture is disabled');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:1000',
        ]);

        $profile->leads()->create($validated);

        return back()->with('success', 'Thank you! Your information has been submitted.');
    }

    public function checkSlug(Request $request)
    {
        $request->validate(['slug' => 'required|string|max:255']);
        $candidate = strtolower(trim($request->slug));

        $reserved = config('reserved_slugs', [
            'admin', 'login', 'register', 'dashboard', 'api', 'pricing', 'support',
            'about', 'contact', 'settings', 'billing', 'profiles', 'analytics', 'templates', 'domains', 'p', 'u'
        ]);

        if (in_array($candidate, $reserved)) {
            return response()->json([
                'available' => false,
                'message' => 'This username is reserved by the system.'
            ]);
        }

        $exists = QRProfile::where('slug', $candidate)->exists();
        if ($exists) {
            return response()->json([
                'available' => false,
                'message' => 'This username is already taken by another user.'
            ]);
        }

        $inHistory = ProfileSlugHistory::where('old_slug', $candidate)->exists();
        if ($inHistory) {
            return response()->json([
                'available' => false,
                'message' => 'This username was previously used and cannot be claimed.'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Username is available!'
        ]);
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

        if ($request->has('download') || $request->wantsJson()) {
            $vcardContent = $this->contactCardService->generateVCard($profile);
            $fileName = str_replace(' ', '_', strtolower($profile->name)) . '.vcf';

            return response($vcardContent, 200, [
                'Content-Type' => 'text/vcard; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        return view('profile.contact', compact('profile'));
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

