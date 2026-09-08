<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\QRProfile;
use App\Models\QRScan;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProfiles = QRProfile::count();
        $totalScans = QRScan::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $recentUsers = User::with('plan')->latest()->take(5)->get();
        $recentProfiles = QRProfile::with('user')->latest()->take(5)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(5)->get();

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalProfiles',
            'totalScans',
            'totalRevenue',
            'activeSubscriptions',
            'recentUsers',
            'recentProfiles',
            'recentAuditLogs'
        ));
    }
}

