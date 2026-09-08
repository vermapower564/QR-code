<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\QRProfile;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function show(int $id, Request $request)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        $range = $request->input('range', '30_days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $stats = $this->analyticsService->getProfileStats($profile, $range, $startDate, $endDate);

        return view('dashboard.profiles.analytics', compact('profile', 'stats', 'range', 'startDate', 'endDate'));
    }

    public function export(int $id, Request $request)
    {
        $profile = Auth::user()->qrProfiles()->findOrFail($id);

        $range = $request->input('range', '30_days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $stats = $this->analyticsService->getProfileStats($profile, $range, $startDate, $endDate);

        $filename = "analytics_" . strtolower($profile->slug) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($profile, $stats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Profile Name', $profile->name]);
            fputcsv($file, ['Profile URL', url("/p/{$profile->slug}")]);
            fputcsv($file, ['Date Range', $stats['start_date'] . ' to ' . $stats['end_date']]);
            fputcsv($file, ['Total QR Scans', $stats['total_scans']]);
            fputcsv($file, ['Unique Visitors', $stats['unique_visitors']]);
            fputcsv($file, ['Total Outbound Clicks', $stats['total_clicks']]);
            fputcsv($file, ['Contact Saves (.VCF)', $stats['save_contact_count']]);
            fputcsv($file, ['Phone Clicks', $stats['phone_clicks']]);
            fputcsv($file, ['Email Clicks', $stats['email_clicks']]);
            fputcsv($file, ['WhatsApp Clicks', $stats['whatsapp_clicks']]);
            fputcsv($file, ['Website Clicks', $stats['website_clicks']]);
            fputcsv($file, ['Growth Rate (%)', $stats['growth_percentage'] . '%']);
            fputcsv($file, []);

            fputcsv($file, ['Device Category', 'Scan Count']);
            foreach ($stats['devices'] as $device => $count) {
                fputcsv($file, [$device, $count]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Country', 'Scan Count']);
            foreach ($stats['countries'] as $country => $count) {
                fputcsv($file, [$country, $count]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
