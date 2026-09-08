<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = QRProfile::with(['user', 'qrCode'])->withCount('scans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $profiles = $query->latest()->paginate(15)->withQueryString();
        return view('admin.profiles.index', compact('profiles'));
    }

    public function show(int $id)
    {
        $profile = QRProfile::with(['user', 'socialLinks', 'customLinks', 'qrCode', 'template'])
            ->withCount('scans')
            ->findOrFail($id);

        return view('admin.profiles.show', compact('profile'));
    }

    public function toggleStatus(Request $request, int $id)
    {
        $profile = QRProfile::findOrFail($id);
        $oldStatus = $profile->status ?? 'active';
        $newStatus = ($oldStatus === 'active') ? 'suspended' : 'active';
        $profile->status = $newStatus;
        $profile->save();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'profile.status_toggle',
            'QRProfile',
            $profile->id,
            "Profile {$profile->name} (/p/{$profile->slug}) status changed to {$newStatus}",
            ['old_status' => $oldStatus, 'new_status' => $newStatus],
            $request->ip()
        );

        return back()->with('success', 'Profile status updated to ' . $newStatus);
    }

    public function destroy(Request $request, int $id)
    {
        $profile = QRProfile::findOrFail($id);
        $name = $profile->name;
        $slug = $profile->slug;

        $profile->delete();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'profile.soft_delete',
            'QRProfile',
            $id,
            "Soft deleted profile {$name} (/p/{$slug})",
            [],
            $request->ip()
        );

        return redirect()->route('admin.profiles.index')->with('success', "Profile {$name} deleted successfully.");
    }
}

