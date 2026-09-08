<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\QRProfile;
use App\Models\QRScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Record a QR Scan event asynchronously with hashed IP and user agent analytics.
     */
    public function recordScan(QRProfile $profile, Request $request): QRScan
    {
        $ip = $request->ip();
        $ipHash = hash('sha256', $ip . config('app.key'));
        $userAgent = $request->userAgent() ?? '';
        $deviceInfo = $this->parseUserAgent($userAgent);

        $scan = QRScan::create([
            'profile_id' => $profile->id,
            'scanned_at' => now(),
            'ip_hash' => $ipHash,
            'country' => $request->header('cf-ipcountry', 'Unknown'),
            'region' => null,
            'city' => null,
            'device' => $deviceInfo['device'],
            'os' => $deviceInfo['os'],
            'browser' => $deviceInfo['browser'],
            'referrer' => $request->header('referer'),
            'user_agent' => substr($userAgent, 0, 500),
        ]);

        AnalyticsEvent::create([
            'profile_id' => $profile->id,
            'event_type' => 'qr_scan',
            'ip_hash' => $ipHash,
            'country' => $scan->country,
            'device' => $scan->device,
            'browser' => $scan->browser,
        ]);

        return $scan;
    }

    /**
     * Record an individual link click event.
     */
    public function recordClick(QRProfile $profile, int $linkId, Request $request, string $eventType = 'link_click'): AnalyticsEvent
    {
        $ip = $request->ip();
        $ipHash = hash('sha256', $ip . config('app.key'));
        $userAgent = $request->userAgent() ?? '';
        $deviceInfo = $this->parseUserAgent($userAgent);

        return AnalyticsEvent::create([
            'profile_id' => $profile->id,
            'event_type' => $eventType,
            'link_id' => $linkId,
            'ip_hash' => $ipHash,
            'country' => $request->header('cf-ipcountry', 'Unknown'),
            'device' => $deviceInfo['device'],
            'browser' => $deviceInfo['browser'],
        ]);
    }

    /**
     * Aggregate detailed analytics statistics for a profile.
     */
    public function getProfileStats(QRProfile $profile): array
    {
        $totalScans = QRScan::where('profile_id', $profile->id)->count();
        $uniqueVisitors = QRScan::where('profile_id', $profile->id)->distinct('ip_hash')->count('ip_hash');
        $totalClicks = AnalyticsEvent::where('profile_id', $profile->id)
            ->where('event_type', 'link_click')
            ->count();
        $saveContactCount = AnalyticsEvent::where('profile_id', $profile->id)
            ->where('event_type', 'contact_save')
            ->count();

        // Chart Data - Last 30 Days Scans
        $dailyScans = QRScan::where('profile_id', $profile->id)
            ->where('scanned_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(scanned_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date')
            ->toArray();

        // Devices Breakdown
        $devices = QRScan::where('profile_id', $profile->id)
            ->select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->pluck('count', 'device')
            ->toArray();

        // Browsers Breakdown
        $browsers = QRScan::where('profile_id', $profile->id)
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->pluck('count', 'browser')
            ->toArray();

        return [
            'total_scans' => $totalScans,
            'unique_visitors' => $uniqueVisitors,
            'total_clicks' => $totalClicks,
            'save_contact_count' => $saveContactCount,
            'daily_scans' => $dailyScans,
            'devices' => $devices,
            'browsers' => $browsers,
        ];
    }

    private function parseUserAgent(string $ua): array
    {
        $device = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $ua)) {
            $device = 'Tablet';
        } elseif (preg_match('/(mobile|iphone|ipod|blackberry|android|palm)/i', $ua)) {
            $device = 'Mobile';
        }

        $os = 'Unknown OS';
        if (preg_match('/windows nt/i', $ua)) $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'macOS';
        elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $os = 'iOS';
        elseif (preg_match('/android/i', $ua)) $os = 'Android';
        elseif (preg_match('/linux/i', $ua)) $os = 'Linux';

        $browser = 'Unknown Browser';
        if (preg_match('/edg/i', $ua)) $browser = 'Edge';
        elseif (preg_match('/chrome/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/firefox/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/safari/i', $ua)) $browser = 'Safari';

        return compact('device', 'os', 'browser');
    }
}
