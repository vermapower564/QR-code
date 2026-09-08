<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\ProfileReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $profile = QRProfile::where('slug', strtolower($slug))->firstOrFail();

        $request->validate([
            'reason' => ['required', 'string', 'in:Spam,Phishing,Malware,Scam,Impersonation,Illegal Content,Abusive Content,Other'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $ip = $request->ip() ?? '127.0.0.1';
        $ipHash = hash('sha256', $ip . config('app.key'));

        ProfileReport::create([
            'profile_id' => $profile->id,
            'reporter_ip_hash' => $ipHash,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thank you for reporting. Our moderation team will review this profile.');
    }
}
