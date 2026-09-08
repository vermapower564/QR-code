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
            ->paginate(10);

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

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:qr_profiles,slug'],
            'designation' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'template_id' => ['nullable', 'exists:templates,id'],
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
            'email' => $request->email,
            'website' => $request->website,
            'profile_image' => $profileImagePath,
            'logo' => $logoPath,
            'template_id' => $request->template_id,
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

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:qr_profiles,slug,' . $profile->id],
            'designation' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
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
            'slug' => strtolower($request->username),
            'name' => $request->name,
            'designation' => $request->designation,
            'company' => $request->company,
            'bio' => $request->bio,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'template_id' => $request->template_id ?? $profile->template_id,
            'theme_data' => array_merge($profile->theme_data ?? [], [
                'bg_color' => $request->input('bg_color', $profile->theme_data['bg_color'] ?? '#f8fafc'),
                'text_color' => $request->input('text_color', $profile->theme_data['text_color'] ?? '#0f172a'),
                'button_style' => $request->input('button_style', $profile->theme_data['button_style'] ?? 'rounded-xl'),
                'theme_preset' => $request->input('theme_preset', $profile->theme_data['theme_preset'] ?? 'Classic'),
            ]),
        ]);

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

    public function addSocialLink(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        $request->validate([
            'platform' => ['required', 'string'],
            'url' => ['required', 'url'],
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
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $profile->customLinks()->create([
            'title' => $request->title,
            'url' => $request->url,
            'description' => $request->description,
            'sort_order' => $profile->customLinks()->count() + 1,
            'status' => 'active',
        ]);

        return back()->with('success', 'Custom button link added!');
    }

    public function deleteSocialLink(int $profileId, int $linkId)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($profileId);
        $profile->socialLinks()->where('id', $linkId)->delete();
        return back()->with('success', 'Social link removed.');
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
}
