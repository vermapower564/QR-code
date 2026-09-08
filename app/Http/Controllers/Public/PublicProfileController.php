<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\SocialLink;
use App\Models\CustomLink;
use App\Services\AnalyticsService;
use App\Services\ContactCardService;
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
        // Reserved keyword check
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

        // Record scan analytics asynchronously
        $this->analyticsService->recordScan($profile, $request);

        return view('profile.show', compact('profile'));
    }

    public function downloadContact(string $slug, Request $request)
    {
        $profile = QRProfile::where('slug', strtolower($slug))->firstOrFail();

        if ($profile->status !== 'active') {
            abort(403, 'Profile is inactive');
        }

        $this->analyticsService->recordClick($profile, 0, $request, 'contact_save');

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

        $targetUrl = null;
        $type = $request->query('type', 'custom');

        if ($type === 'social') {
            $link = SocialLink::where('profile_id', $profile->id)->findOrFail($linkId);
            $targetUrl = $link->url;
            $this->analyticsService->recordClick($profile, $linkId, $request, 'social_click');
        } else {
            $link = CustomLink::where('profile_id', $profile->id)->findOrFail($linkId);
            $targetUrl = $link->url;
            $this->analyticsService->recordClick($profile, $linkId, $request, 'link_click');
        }

        return redirect()->away($targetUrl);
    }
}
