<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\Template;
use App\Services\FeatureService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function __construct(
        protected FeatureService $featureService,
        protected QRCodeService $qrCodeService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->onboarding_completed) {
            return redirect()->route('dashboard.index');
        }

        $existingProfile = $user->qrProfiles()->with(['socialLinks', 'customLinks', 'qrCode'])->latest()->first();
        $templates = Template::where('status', 'active')->get();

        return view('onboarding.index', compact('existingProfile', 'templates'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->onboarding_completed) {
            return redirect()->route('dashboard.index');
        }

        $existingProfile = $user->qrProfiles()->latest()->first();

        if (!$existingProfile && !$this->featureService->canCreateProfile($user)) {
            return back()->with('error', 'Profile creation limit reached for your current plan.');
        }

        $profileId = $existingProfile ? $existingProfile->id : null;

        // URL Normalization and Validation
        $linkValidator = app(\App\Services\LinkValidationService::class);
        
        if ($request->filled('website')) {
            $normalizedWebsite = $linkValidator->normalizeUrl($request->website);
            if (!$linkValidator->isValidUrl($normalizedWebsite)) {
                return back()->withErrors(['website' => 'Please enter a valid website URL.'])->withInput();
            }
            $request->merge(['website' => $normalizedWebsite]);
        }

        if ($request->has('social') && is_array($request->social)) {
            $socials = $request->input('social');
            foreach ($socials as $platform => $url) {
                if (!empty($url)) {
                    $normalizedUrl = $linkValidator->normalizeUrl($url);
                    if (!$linkValidator->isValidUrl($normalizedUrl)) {
                        return back()->withErrors(['social.' . $platform => "Please enter a valid URL for " . ucfirst($platform)])->withInput();
                    }
                    $socials[$platform] = $normalizedUrl;
                }
            }
            $request->merge(['social' => $socials]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:qr_profiles,slug,' . $profileId],
            'designation' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:2048'],
            'template_id' => ['nullable', 'exists:templates,id'],
        ], [
            'phone.regex' => 'Mobile number must be strictly 10 digits (0-9).',
        ]);

        $reserved = ['admin', 'login', 'register', 'dashboard', 'api', 'pricing', 'support', 'about', 'contact', 'settings', 'onboarding'];
        if (in_array(strtolower($request->username), $reserved)) {
            if ($request->ajax() || $request->input('is_draft')) {
                return response()->json(['success' => false, 'errors' => ['username' => ['This username is reserved. Please select another one.']]], 422);
            }
            return back()->withErrors(['username' => 'This username is reserved. Please select another one.']);
        }

        try {
            DB::beginTransaction();

            $slug = strtolower($request->username);

            if ($existingProfile) {
                \Illuminate\Support\Facades\Cache::forget('profile:' . strtolower($existingProfile->slug));
                \Illuminate\Support\Facades\Cache::forget('profile:' . strtolower($slug));
                $existingProfile->update([
                    'slug' => $slug,
                    'name' => $request->name,
                    'designation' => $request->designation,
                    'company' => $request->company,
                    'bio' => $request->bio,
                    'phone' => $request->phone,
                    'whatsapp' => $request->whatsapp,
                    'email' => $request->email,
                    'website' => $request->website ?: $user->website,
                    'template_id' => $request->template_id,
                    'status' => 'active',
                ]);
                $profile = $existingProfile;
            } else {
                \Illuminate\Support\Facades\Cache::forget('profile:' . strtolower($slug));
                $profile = QRProfile::create([
                    'user_id' => $user->id,
                    'slug' => $slug,
                    'name' => $request->name,
                    'designation' => $request->designation,
                    'company' => $request->company,
                    'bio' => $request->bio,
                    'phone' => $request->phone,
                    'whatsapp' => $request->whatsapp,
                    'email' => $request->email,
                    'website' => $request->website ?: $user->website,
                    'template_id' => $request->template_id,
                    'theme_data' => [
                        'bg_color' => '#f8fafc',
                        'text_color' => '#0f172a',
                        'button_style' => 'rounded-xl',
                        'theme_preset' => 'Business',
                    ],
                    'status' => 'active',
                ]);
            }

            // Sync Social Links
            if ($request->has('social')) {
                $socials = $request->input('social');
                $platforms = [
                    'instagram' => ['title' => 'Instagram', 'icon' => 'fa-brands fa-instagram'],
                    'facebook' => ['title' => 'Facebook', 'icon' => 'fa-brands fa-facebook'],
                    'linkedin' => ['title' => 'LinkedIn', 'icon' => 'fa-brands fa-linkedin'],
                    'youtube' => ['title' => 'YouTube', 'icon' => 'fa-brands fa-youtube'],
                ];
                
                $order = 0;
                foreach ($platforms as $key => $info) {
                    if (!empty($socials[$key])) {
                        $profile->socialLinks()->updateOrCreate(
                            ['platform' => $key],
                            [
                                'title' => $info['title'],
                                'url' => $socials[$key],
                                'icon' => $info['icon'],
                                'sort_order' => $order++,
                                'status' => 'active'
                            ]
                        );
                    } else {
                        $profile->socialLinks()->where('platform', $key)->delete();
                    }
                }
            }

            if ($request->ajax() || $request->input('is_draft')) {
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Draft saved']);
            }

            // Generate dynamic QR Code
            $this->qrCodeService->generate($profile);

            // Mark onboarding as completed only after successful profile + QR creation
            $user->onboarding_completed = true;
            $user->save();

            DB::commit();

            return redirect()->route('onboarding.complete');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->input('is_draft')) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => 'Failed to create profile or QR code. Please try again: ' . $e->getMessage()]);
        }
    }

    public function complete(Request $request)
    {
        $user = Auth::user();

        $profile = $user->qrProfiles()->with(['qrCode'])->latest()->first();

        if (!$profile) {
            return redirect()->route('onboarding.index');
        }

        return view('onboarding.complete', compact('profile'));
    }
}



