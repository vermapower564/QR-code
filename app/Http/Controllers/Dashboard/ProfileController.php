<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\SocialLink;
use App\Models\CustomLink;
use App\Models\Template;
use App\Services\FeatureService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct(
        protected FeatureService $featureService,
        protected QRCodeService $qrCodeService
    ) {}

    public function index()
    {
        $profiles = Auth::user()->qrProfiles()
            ->withCount('scans')
            ->with('qrCode')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.profiles.index', compact('profiles'));
    }

    public function create()
    {
        if (!$this->featureService->canCreateProfile(Auth::user())) {
            return redirect()->route('dashboard.profiles.index')
                ->with('error', 'Profile creation limit reached for your plan. Upgrade your plan to create more profiles.');
        }

        $templates = Template::where('status', 'active')->get();
        return view('dashboard.profiles.create', compact('templates'));
    }

    public function store(Request $request)
    {
        if (!$this->featureService->canCreateProfile(Auth::user())) {
            return back()->with('error', 'Plan limit reached.');
        }

        if ($request->filled('website')) {
            $linkValidator = app(\App\Services\LinkValidationService::class);
            $normalizedWebsite = $linkValidator->normalizeUrl($request->website);
            if (!$linkValidator->isValidUrl($normalizedWebsite)) {
                return back()->withErrors(['website' => 'Please enter a valid website URL.'])->withInput();
            }
            $request->merge(['website' => $normalizedWebsite]);
        }

        if ($request->has('social') && is_array($request->social)) {
            $linkValidator = app(\App\Services\LinkValidationService::class);
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
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:qr_profiles,slug'],
            'designation' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:2048'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'template_id' => ['nullable', 'exists:templates,id'],
            'profile_password' => ['nullable', 'string', 'max:255'],
            'enable_lead_capture' => ['nullable', 'boolean'],
        ], [
            'phone.regex' => 'Mobile number must be strictly 10 digits (0-9).',
            'website.url' => 'Please enter a valid website URL.',
        ]);

        // Reserved slug validation
        $reserved = ['admin', 'login', 'register', 'dashboard', 'api', 'pricing', 'support', 'about', 'contact', 'settings'];
        if (in_array(strtolower($request->username), $reserved)) {
            return back()->withErrors(['username' => 'This username is reserved. Please choose another one.']);
        }

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profiles/images', 'public');
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('profiles/logos', 'public');
        }

        $profile = QRProfile::create([
            'user_id' => Auth::id(),
            'slug' => strtolower($request->username),
            'name' => $request->name,
            'designation' => $request->designation,
            'company' => $request->company,
            'bio' => $request->bio,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'website' => $request->website,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'address' => $request->address,
            'profile_image' => $profileImagePath,
            'logo' => $logoPath,
            'template_id' => $request->template_id,
            'profile_password' => $request->profile_password,
            'enable_lead_capture' => $request->boolean('enable_lead_capture'),
            'theme_data' => [
                'bg_color' => $request->input('bg_color', '#f8fafc'),
                'text_color' => $request->input('text_color', '#0f172a'),
                'button_style' => $request->input('button_style', 'rounded-xl'),
                'theme_preset' => $request->input('theme_preset', 'Classic'),
            ],
            'status' => 'active',
        ]);

        // Automatically generate dynamic QR Code for this profile
        $this->qrCodeService->generate($profile);

        return redirect()->route('dashboard.profiles.edit', $profile->id)
            ->with('success', 'Profile created! Now add your social links & customize your QR code.');
    }

    public function edit(int $id)
    {
        $profile = Auth::user()->qrProfiles()
            ->with(['socialLinks', 'customLinks', 'qrCode'])
            ->findOrFail($id);

        $templates = Template::where('status', 'active')->get();

        return view('dashboard.profiles.edit', compact('profile', 'templates'));
    }

    public function update(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        if ($request->filled('website')) {
            $linkValidator = app(\App\Services\LinkValidationService::class);
            $normalizedWebsite = $linkValidator->normalizeUrl($request->website);
            if (!$linkValidator->isValidUrl($normalizedWebsite)) {
                return back()->withErrors(['website' => 'Please enter a valid website URL.'])->withInput();
            }
            $request->merge(['website' => $normalizedWebsite]);
        }

        if ($request->has('social') && is_array($request->social)) {
            $linkValidator = app(\App\Services\LinkValidationService::class);
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
            'designation' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:2048'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'profile_password' => ['nullable', 'string', 'max:255'],
            'enable_lead_capture' => ['nullable', 'boolean'],
        ], [
            'phone.regex' => 'Mobile number must be strictly 10 digits (0-9).',
            'website.url' => 'Please enter a valid website URL.',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $profile->profile_image = $request->file('profile_image')->store('profiles/images', 'public');
        }

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $profile->logo = $request->file('logo')->store('profiles/logos', 'public');
        }

        $profile->update([
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'name' => $request->name,
            'designation' => $request->designation,
            'company' => $request->company,
            'bio' => $request->bio,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'website' => $request->website,
            'address' => $request->address,
            'template_id' => $request->template_id ?? $profile->template_id,
            'profile_password' => $request->profile_password,
            'enable_lead_capture' => $request->boolean('enable_lead_capture'),
            'theme_data' => array_merge($profile->theme_data ?? [], [
                'bg_color' => $request->input('bg_color', $profile->theme_data['bg_color'] ?? '#f8fafc'),
                'text_color' => $request->input('text_color', $profile->theme_data['text_color'] ?? '#0f172a'),
                'button_style' => $request->input('button_style', $profile->theme_data['button_style'] ?? 'rounded-xl'),
                'theme_preset' => $request->input('theme_preset', $profile->theme_data['theme_preset'] ?? 'Classic'),
            ]),
        ]);

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

        return back()->with('success', 'Profile updated successfully!');
    }

    public function toggleStatus(int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);
        $profile->status = ($profile->status === 'active') ? 'inactive' : 'active';
        $profile->save();

        return back()->with('success', 'Profile status updated to ' . $profile->status);
    }

    public function duplicate(int $id)
    {
        if (!$this->featureService->canCreateProfile(Auth::user())) {
            return back()->with('error', 'Profile limit reached.');
        }

        $source = Auth::user()->qrProfiles()->with(['socialLinks', 'customLinks'])->findOrFail($id);

        $newSlug = $source->slug . '-copy-' . rand(100, 999);

        $replica = $source->replicate(['slug', 'created_at', 'updated_at']);
        $replica->slug = $newSlug;
        $replica->name = $source->name . ' (Copy)';
        $replica->save();

        foreach ($source->socialLinks as $link) {
            $replica->socialLinks()->create($link->only(['platform', 'title', 'url', 'icon', 'sort_order', 'status']));
        }

        foreach ($source->customLinks as $link) {
            $replica->customLinks()->create($link->only(['title', 'description', 'url', 'icon', 'sort_order', 'status']));
        }

        $this->qrCodeService->generate($replica);

        return redirect()->route('dashboard.profiles.index')->with('success', 'Profile duplicated successfully!');
    }

    public function leads(int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);
        $leads = $profile->leads()->paginate(15);
        return view('dashboard.profiles.leads', compact('profile', 'leads'));
    }

    public function addSocialLink(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        $request->validate([
            'platform' => ['required', 'string'],
            'url' => ['required', 'string', new \App\Rules\SafeUrl],
            'title' => ['nullable', 'string'],
        ]);

        $profile->socialLinks()->create([
            'platform' => strtolower($request->platform),
            'title' => $request->title ?: ucfirst($request->platform),
            'url' => $request->url,
            'sort_order' => $profile->socialLinks()->count() + 1,
            'status' => 'active',
        ]);

        return back()->with('success', 'Social link added!');
    }

    public function addCustomLink(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        if (!$this->featureService->canAddCustomLink(Auth::user(), $profile)) {
            return back()->with('error', 'Custom link limit reached for your current plan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', new \App\Rules\SafeUrl],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $profile->customLinks()->create([
            'title' => $request->title,
            'url' => $request->url,
            'description' => $request->description,
            'icon' => $request->icon,
            'sort_order' => $profile->customLinks()->count() + 1,
            'status' => 'active',
        ]);

        return back()->with('success', 'Custom button link added!');
    }

    public function reorderCustomLinks(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);
        $orders = $request->input('orders', []); // Expected: [link_id => sort_order]

        foreach ($orders as $linkId => $sortOrder) {
            $profile->customLinks()->where('id', $linkId)->update(['sort_order' => $sortOrder]);
        }

        return response()->json(['success' => true]);
    }


    public function deleteSocialLink(int $profileId, int $linkId)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($profileId);
        $profile->socialLinks()->where('id', $linkId)->delete();
        return back()->with('success', 'Social link removed.');
    }

    public function updateCustomLink(Request $request, int $profileId, int $linkId)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($profileId);
        $link = $profile->customLinks()->findOrFail($linkId);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', new \App\Rules\SafeUrl],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $link->update([
            'title' => $request->title,
            'url' => $request->url,
            'description' => $request->description,
            'icon' => $request->icon,
        ]);

        return back()->with('success', 'Custom link updated!');
    }

    public function deleteCustomLink(int $profileId, int $linkId)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($profileId);
        $profile->customLinks()->where('id', $linkId)->delete();
        return back()->with('success', 'Custom link removed.');
    }

    public function destroy(int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);
        $profile->delete();

        return redirect()->route('dashboard.profiles.index')->with('success', 'Profile deleted.');
    }

    public function exportJson(int $id)
    {
        $profile = Auth::user()->qrProfiles()
            ->with(['socialLinks', 'customLinks', 'qrCode'])
            ->findOrFail($id);

        $data = [
            'id' => $profile->id,
            'slug' => $profile->slug,
            'name' => $profile->name,
            'designation' => $profile->designation,
            'company' => $profile->company,
            'bio' => $profile->bio,
            'phone' => $profile->phone,
            'email' => $profile->email,
            'website' => $profile->website,
            'status' => $profile->status,
            'created_at' => $profile->created_at?->toIso8601String(),
            'social_links' => $profile->socialLinks->map(fn($link) => [
                'platform' => $link->platform,
                'title' => $link->title,
                'url' => $link->url,
            ]),
            'custom_links' => $profile->customLinks->map(fn($link) => [
                'title' => $link->title,
                'description' => $link->description,
                'url' => $link->url,
            ]),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"profile-{$profile->slug}.json\"",
        ]);
    }

    public function exportCsv(int $id)
    {
        $profile = Auth::user()->qrProfiles()
            ->with(['socialLinks', 'customLinks'])
            ->findOrFail($id);

        $validator = app(\App\Services\LinkValidationService::class);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"profile-{$profile->slug}.csv\"",
        ];

        $callback = function () use ($profile, $validator) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Field', 'Value']);

            fputcsv($file, ['ID', $profile->id]);
            fputcsv($file, ['Slug', $validator->sanitizeCsvCell($profile->slug)]);
            fputcsv($file, ['Name', $validator->sanitizeCsvCell($profile->name)]);
            fputcsv($file, ['Designation', $validator->sanitizeCsvCell($profile->designation)]);
            fputcsv($file, ['Company', $validator->sanitizeCsvCell($profile->company)]);
            fputcsv($file, ['Bio', $validator->sanitizeCsvCell($profile->bio)]);
            fputcsv($file, ['Phone', $validator->sanitizeCsvCell($profile->phone)]);
            fputcsv($file, ['Email', $validator->sanitizeCsvCell($profile->email)]);
            fputcsv($file, ['Website', $validator->sanitizeCsvCell($profile->website)]);
            fputcsv($file, ['Status', $profile->status]);

            foreach ($profile->socialLinks as $index => $link) {
                fputcsv($file, ["Social Link #" . ($index + 1), $validator->sanitizeCsvCell("{$link->platform}: {$link->url}")]);
            }

            foreach ($profile->customLinks as $index => $link) {
                fputcsv($file, ["Custom Link #" . ($index + 1), $validator->sanitizeCsvCell("{$link->title}: {$link->url}")]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}



