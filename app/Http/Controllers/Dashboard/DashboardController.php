<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Models\QRScan;
use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profileIds = $user->qrProfiles()->pluck('id');

        $totalProfiles = $profileIds->count();
        $activeProfiles = $user->qrProfiles()->where('status', 'active')->count();

        $totalScans = QRScan::whereIn('profile_id', $profileIds)->count();
        $uniqueVisitors = QRScan::whereIn('profile_id', $profileIds)->distinct('ip_hash')->count('ip_hash');
        $totalLinkClicks = AnalyticsEvent::whereIn('profile_id', $profileIds)
            ->where('event_type', 'link_click')
            ->count();

        $recentProfiles = $user->qrProfiles()
            ->withCount('scans')
            ->latest()
            ->take(5)
            ->get();

        // 30 day scans aggregated chart data
        $scansOverTime = QRScan::whereIn('profile_id', $profileIds)
            ->where('scanned_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(scanned_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date')
            ->toArray();

        return view('dashboard.index', compact(
            'totalProfiles',
            'activeProfiles',
            'totalScans',
            'uniqueVisitors',
            'totalLinkClicks',
            'recentProfiles',
            'scansOverTime'
        ));
    }
}
