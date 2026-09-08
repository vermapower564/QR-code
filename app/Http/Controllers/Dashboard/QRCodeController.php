<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QRCodeController extends Controller
{
    public function __construct(
        protected QRCodeService $qrCodeService
    ) {}

    public function show(int $id)
    {
        $profile = Auth::user()->qrProfiles()->with('qrCode')->findOrFail($id);
        
        if (!$profile->qrCode) {
            $this->qrCodeService->generate($profile);
            $profile->load('qrCode');
        }

        return view('dashboard.profiles.qr', compact('profile'));
    }

    public function update(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        $request->validate([
            'foreground_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'background_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'style' => ['required', 'in:square,rounded,dots'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        $logoPath = $profile->qrCode ? $profile->qrCode->logo_path : null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('qr-logos', 'public');
        }

        $this->qrCodeService->generate($profile, [
            'foreground_color' => $request->foreground_color,
            'background_color' => $request->background_color,
            'style' => $request->style,
            'logo_path' => $logoPath,
            'format' => 'png',
            'size' => 1024,
        ]);

        return back()->with('success', 'QR Code styling updated successfully!');
    }

    public function download(Request $request, int $id)
    {
        $profile = Auth::user()->qrProfiles()->with('qrCode')->findOrFail($id);

        $format = strtolower($request->query('format', 'png'));
        $size = (int) $request->query('size', 512); // 512, 1024, 2048

        if ($format === 'pdf') {
            $pdfContent = $this->qrCodeService->generatePdfFrame($profile, $request->query('label', 'SCAN TO VIEW PROFILE'));
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="qr-' . $profile->slug . '.pdf"',
            ]);
        }

        $qrCode = $this->qrCodeService->generate($profile, [
            'foreground_color' => $profile->qrCode->foreground_color ?? '#000000',
            'background_color' => $profile->qrCode->background_color ?? '#ffffff',
            'style' => $profile->qrCode->style ?? 'square',
            'logo_path' => $profile->qrCode->logo_path ?? null,
            'format' => $format,
            'size' => $size,
        ]);

        $fullPath = Storage::disk('public')->path($qrCode->file_path);
        return response()->download($fullPath, 'qr-' . $profile->slug . '-' . $size . '.' . $format);
    }
}
