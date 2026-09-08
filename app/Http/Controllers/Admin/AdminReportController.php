<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\QRProfile;
use App\Models\QRScan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function index()
    {
        $scanStatsByBrowser = QRScan::selectRaw('browser, count(*) as count')->groupBy('browser')->orderByDesc('count')->take(5)->get();
        $scanStatsByDevice = QRScan::selectRaw('device_type, count(*) as count')->groupBy('device_type')->orderByDesc('count')->take(5)->get();
        $scanStatsByCountry = QRScan::selectRaw('country, count(*) as count')->groupBy('country')->orderByDesc('count')->take(5)->get();

        return view('admin.reports.index', compact('scanStatsByBrowser', 'scanStatsByDevice', 'scanStatsByCountry'));
    }

    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'users');
        $fileName = "report-{$type}-" . date('Y-m-d') . ".csv";

        $response = new StreamedResponse(function() use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'users') {
                fputcsv($handle, ['ID', 'Name', 'Email', 'Status', 'Plan ID', 'Created At']);
                User::chunk(100, function($users) use ($handle) {
                    foreach ($users as $user) {
                        fputcsv($handle, [$user->id, $user->name, $user->email, $user->status, $user->plan_id, $user->created_at]);
                    }
                });
            } elseif ($type === 'profiles') {
                fputcsv($handle, ['ID', 'User ID', 'Name', 'Slug', 'Status', 'Company', 'Scans Count', 'Created At']);
                QRProfile::withCount('scans')->chunk(100, function($profiles) use ($handle) {
                    foreach ($profiles as $profile) {
                        fputcsv($handle, [$profile->id, $profile->user_id, $profile->name, $profile->slug, $profile->status, $profile->company, $profile->scans_count, $profile->created_at]);
                    }
                });
            } else {
                fputcsv($handle, ['ID', 'Profile ID', 'IP Hash', 'Device', 'Browser', 'Country', 'Scanned At']);
                QRScan::chunk(100, function($scans) use ($handle) {
                    foreach ($scans as $scan) {
                        fputcsv($handle, [$scan->id, $scan->profile_id, $scan->ip_hash, $scan->device_type, $scan->browser, $scan->country, $scan->created_at]);
                    }
                });
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$fileName}\"");

        return $response;
    }
}
