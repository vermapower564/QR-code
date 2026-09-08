<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Services\AnalyticsService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected QRCodeService $qrCodeService
    ) {}

    public function index(Request $request)
    {
        $profiles = $request->user()->qrProfiles()->with(['socialLinks', 'customLinks', 'qrCode'])->paginate(15);
        return response()->json(['success' => true, 'data' => $profiles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|alpha_dash|unique:qr_profiles,slug',
            'designation' => 'nullable|string',
            'company' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $profile = $request->user()->qrProfiles()->create([
            'slug' => strtolower($request->slug),
            'name' => $request->name,
            'designation' => $request->designation,
            'company' => $request->company,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'status' => 'active',
        ]);

        $this->qrCodeService->generate($profile);

        return response()->json(['success' => true, 'message' => 'Profile created successfully.', 'data' => $profile->load('qrCode')], 201);
    }

    public function show(Request $request, int $id)
    {
        $profile = $request->user()->qrProfiles()->with(['socialLinks', 'customLinks', 'qrCode'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $profile]);
    }

    public function update(Request $request, int $id)
    {
        $profile = $request->user()->qrProfiles()->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'designation' => 'nullable|string',
            'company' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $profile->update($request->only(['name', 'designation', 'company', 'phone', 'email', 'website']));

        return response()->json(['success' => true, 'message' => 'Profile updated.', 'data' => $profile]);
    }

    public function destroy(Request $request, int $id)
    {
        $profile = $request->user()->qrProfiles()->findOrFail($id);
        $profile->delete();

        return response()->json(['success' => true, 'message' => 'Profile deleted.']);
    }

    public function analytics(Request $request, int $id)
    {
        $profile = $request->user()->qrProfiles()->findOrFail($id);
        $stats = $this->analyticsService->getProfileStats($profile);

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
