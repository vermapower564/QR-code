<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
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

        $profiles = $query->latest()->paginate(15);
        return view('admin.profiles.index', compact('profiles'));
    }

    public function toggleStatus(int $id)
    {
        $profile = QRProfile::findOrFail($id);
        $profile->status = ($profile->status === 'active') ? 'suspended' : 'active';
        $profile->save();

        return back()->with('success', 'Profile status updated to ' . $profile->status);
    }
}
