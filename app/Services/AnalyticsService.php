<?php

namespace App\Services;

use App\Constants\AnalyticsEvents;
use App\Models\AnalyticsEvent;
use App\Models\QRProfile;
use App\Models\QRScan;
use App\Models\SocialLink;
use App\Models\CustomLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Record a QR Scan event with salted IP hashing, UA parsing, geo parsing & bot detection.
     */
    public function recordScan(QRProfile $profile, Request $request): QRScan
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $ipHash = hash('sha256', $ip . config('app.key'));
        $userAgent = $request->userAgent() ?? '';
        $uaHash = hash('sha256', $userAgent);
        $deviceInfo = $this->parseUserAgent($userAgent);
        $isBot = $this->detectBot($userAgent);

        $country = $request->header('cf-ipcountry') ?? $request->header('x-country') ?? 'Unknown';
        $region = $request->header('x-region') ?? null;
        $city = $request->header('x-city') ?? null;
        $referrer = $this->normalizeReferrer($request->header('referer'));

        $scan = QRScan::create([
            'profile_id' => $profile->id,
            'scanned_at' => now(),
            'ip_hash' => $ipHash,
            'country' => $country,
            'region' => $region,
            'city' => $city,
            'device' => $isBot ? 'Bot' : $deviceInfo['device'],
            'os' => $deviceInfo['os'],
            'browser' => $deviceInfo['browser'],
            'referrer' => $referrer,
            'user_agent' => substr($userAgent, 0, 500),
        ]);

        AnalyticsEvent::create([
            'profile_id' => $profile->id,
            'event_type' => AnalyticsEvents::QR_SCAN,
            'ip_hash' => $ipHash,
            'user_agent_hash' => $uaHash,
            'country' => $country,
            'region' => $region,
            'city' => $city,
            'device' => $scan->device,
            'os' => $scan->os,
            'browser' => $scan->browser,
            'referrer' => $referrer,
            'is_bot' => $isBot,
            'occurred_at' => now(),
            'metadata' => ['source' => 'qr'],
        ]);

        Cache::forget("profile_stats_{$profile->id}_30_days");

        return $scan;
    }

    /**
     * Record an individual interaction click event.
     */
    public function recordClick(
        QRProfile $profile,
        int $linkId,
        Request $request,
        string $eventType = AnalyticsEvents::LINK_CLICK,
        array $metadata = []
    ): AnalyticsEvent {
        $ip = $request->ip() ?? '127.0.0.1';
        $ipHash = hash('sha256', $ip . config('app.key'));
        $userAgent = $request->userAgent() ?? '';
        $uaHash = hash('sha256', $userAgent);
        $deviceInfo = $this->parseUserAgent($userAgent);
        $isBot = $this->detectBot($userAgent);

        $country = $request->header('cf-ipcountry') ?? $request->header('x-country') ?? 'Unknown';
        $region = $request->header('x-region') ?? null;
        $city = $request->header('x-city') ?? null;

        $event = AnalyticsEvent::create([
            'profile_id' => $profile->id,
            'event_type' => $eventType,
            'link_id' => $linkId,
            'ip_hash' => $ipHash,
            'user_agent_hash' => $uaHash,
            'country' => $country,
            'region' => $region,
            'city' => $city,
            'device' => $isBot ? 'Bot' : $deviceInfo['device'],
            'os' => $deviceInfo['os'],
            'browser' => $deviceInfo['browser'],
            'referrer' => $this->normalizeReferrer($request->header('referer')),
            'is_bot' => $isBot,
            'occurred_at' => now(),
            'metadata' => $metadata,
        ]);

        Cache::forget("profile_stats_{$profile->id}_30_days");

        return $event;
    }

    /**
     * Aggregate detailed analytics statistics for a profile with date filtering, growth calculation & breakdowns.
     */
    public function getProfileStats(QRProfile $profile, string $range = '30_days', ?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = "profile_stats_{$profile->id}_{$range}_" . md5($startDate . $endDate);

        return Cache::remember($cacheKey, 300, function () use ($profile, $range, $startDate, $endDate) {
            $dates = $this->resolveDateRange($range, $startDate, $endDate);
            $start = $dates['start'];
            $end = $dates['end'];

            // Query Scans within period
            $scansQuery = QRScan::where('profile_id', $profile->id)
                ->whereBetween('scanned_at', [$start, $end]);

            $totalScans = (clone $scansQuery)->count();
            $uniqueVisitors = (clone $scansQuery)->distinct('ip_hash')->count('ip_hash');

            // Outbound Clicks
            $totalClicks = AnalyticsEvent::where('profile_id', $profile->id)
                ->whereIn('event_type', [AnalyticsEvents::LINK_CLICK, 'social_click', AnalyticsEvents::WEBSITE_CLICK])
                ->whereBetween('created_at', [$start, $end])
                ->count();

            // Contact Saves
            $saveContactCount = AnalyticsEvent::where('profile_id', $profile->id)
                ->where('event_type', AnalyticsEvents::CONTACT_SAVE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            // Additional Action Counts
            $phoneClicks = AnalyticsEvent::where('profile_id', $profile->id)->where('event_type', AnalyticsEvents::PHONE_CLICK)->whereBetween('created_at', [$start, $end])->count();
            $emailClicks = AnalyticsEvent::where('profile_id', $profile->id)->where('event_type', AnalyticsEvents::EMAIL_CLICK)->whereBetween('created_at', [$start, $end])->count();
            $whatsappClicks = AnalyticsEvent::where('profile_id', $profile->id)->where('event_type', AnalyticsEvents::WHATSAPP_CLICK)->whereBetween('created_at', [$start, $end])->count();
            $websiteClicks = AnalyticsEvent::where('profile_id', $profile->id)->where('event_type', AnalyticsEvents::WEBSITE_CLICK)->whereBetween('created_at', [$start, $end])->count();
            $sharesCount = AnalyticsEvent::where('profile_id', $profile->id)->where('event_type', AnalyticsEvents::SHARE)->whereBetween('created_at', [$start, $end])->count();

            // Period Growth Rate Calculation (% change vs previous period)
            $diffInDays = max(1, $start->diffInDays($end));
            $prevStart = (clone $start)->subDays($diffInDays);
            $prevEnd = (clone $start)->subSecond();

            $prevScans = QRScan::where('profile_id', $profile->id)
                ->whereBetween('scanned_at', [$prevStart, $prevEnd])
                ->count();

            $growthPercentage = $prevScans > 0
                ? round((($totalScans - $prevScans) / $prevScans) * 100, 1)
                : ($totalScans > 0 ? 100 : 0);

            // Daily Scan Trends
            $dailyScans = (clone $scansQuery)
                ->select(DB::raw('DATE(scanned_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->pluck('count', 'date')
                ->toArray();

            // Country Breakdown
            $countries = (clone $scansQuery)
                ->select('country', DB::raw('count(*) as count'))
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'country')
                ->toArray();

            // City Breakdown
            $cities = (clone $scansQuery)
                ->whereNotNull('city')
                ->select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'city')
                ->toArray();

            // Devices Breakdown
            $devices = (clone $scansQuery)
                ->select('device', DB::raw('count(*) as count'))
                ->groupBy('device')
                ->pluck('count', 'device')
                ->toArray();

            // OS Breakdown
            $osBreakdown = (clone $scansQuery)
                ->select('os', DB::raw('count(*) as count'))
                ->groupBy('os')
                ->pluck('count', 'os')
                ->toArray();

            // Browsers Breakdown
            $browsers = (clone $scansQuery)
                ->select('browser', DB::raw('count(*) as count'))
                ->groupBy('browser')
                ->pluck('count', 'browser')
                ->toArray();

            // Referrers Breakdown
            $referrers = (clone $scansQuery)
                ->select('referrer', DB::raw('count(*) as count'))
                ->groupBy('referrer')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'referrer')
                ->toArray();

            // Top Links
            $topLinks = AnalyticsEvent::where('profile_id', $profile->id)
                ->whereNotNull('link_id')
                ->where('link_id', '>', 0)
                ->whereBetween('created_at', [$start, $end])
                ->select('link_id', DB::raw('count(*) as count'))
                ->groupBy('link_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($row) use ($profile) {
                    $link = CustomLink::where('profile_id', $profile->id)->find($row->link_id)
                        ?? SocialLink::where('profile_id', $profile->id)->find($row->link_id);
                    return [
                        'link_id' => $row->link_id,
                        'title' => $link ? ($link->title ?? $link->platform ?? 'Link #' . $row->link_id) : 'Link #' . $row->link_id,
                        'url' => $link ? $link->url : '#',
                        'clicks' => $row->count,
                    ];
                })
                ->toArray();

            return [
                'total_scans' => $totalScans,
                'unique_visitors' => $uniqueVisitors,
                'total_clicks' => $totalClicks,
                'save_contact_count' => $saveContactCount,
                'phone_clicks' => $phoneClicks,
                'email_clicks' => $emailClicks,
                'whatsapp_clicks' => $whatsappClicks,
                'website_clicks' => $websiteClicks,
                'shares_count' => $sharesCount,
                'growth_percentage' => $growthPercentage,
                'daily_scans' => $dailyScans,
                'countries' => $countries,
                'cities' => $cities,
                'devices' => $devices,
                'os_breakdown' => $osBreakdown,
                'browsers' => $browsers,
                'referrers' => $referrers,
                'top_links' => $topLinks,
                'range' => $range,
                'start_date' => $start->toFormattedDateString(),
                'end_date' => $end->toFormattedDateString(),
            ];
        });
    }

    private function resolveDateRange(string $range, ?string $startDate, ?string $endDate): array
    {
        $end = now()->endOfDay();

        switch ($range) {
            case 'today':
                $start = now()->startOfDay();
                break;
            case '7_days':
                $start = now()->subDays(7)->startOfDay();
                break;
            case 'this_month':
                $start = now()->startOfMonth();
                break;
            case 'last_month':
                $start = now()->subMonth()->startOfMonth();
                $end = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $start = now()->startOfYear();
                break;
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->subDays(30)->startOfDay();
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
                break;
            case '30_days':
            default:
                $start = now()->subDays(30)->startOfDay();
                break;
        }

        return compact('start', 'end');
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
        elseif (preg_match('/samsungbrowser/i', $ua)) $browser = 'Samsung Internet';
        elseif (preg_match('/opr|opera/i', $ua)) $browser = 'Opera';
        elseif (preg_match('/chrome/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/firefox/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/safari/i', $ua)) $browser = 'Safari';

        return compact('device', 'os', 'browser');
    }

    private function detectBot(string $ua): bool
    {
        if (empty($ua)) {
            return false;
        }

        return (bool) preg_match('/(googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|facebookexternalhit|twitterbot|rogerbot|linkedinbot|embedly|quora link preview|showyouhavebot|outbrain|pinterest|slackbot|vkShare|W3C_Validator|curl|python|wget|headlesschrome)/i', $ua);
    }

    private function normalizeReferrer(?string $referer): string
    {
        if (empty($referer)) {
            return 'Direct QR Scan';
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (!$host) {
            return 'Direct QR Scan';
        }

        $host = strtolower($host);
        if (str_contains($host, 'instagram.com')) return 'Instagram';
        if (str_contains($host, 'facebook.com')) return 'Facebook';
        if (str_contains($host, 'google.com')) return 'Google';
        if (str_contains($host, 'linkedin.com')) return 'LinkedIn';
        if (str_contains($host, 'twitter.com') || str_contains($host, 'x.com')) return 'X (Twitter)';
        if (str_contains($host, 'whatsapp.com')) return 'WhatsApp';

        return $host;
    }
}
