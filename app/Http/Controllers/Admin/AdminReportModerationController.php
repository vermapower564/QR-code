<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileReport;
use App\Models\QRProfile;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminReportModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = ProfileReport::with('profile.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(15);
        return view('admin.profile_reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:pending,reviewed,dismissed']);

        $report = ProfileReport::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'report.update_status',
            'ProfileReport',
            $report->id,
            "Updated report #{$report->id} status to {$request->status}",
            [],
            $request->ip()
        );

        return back()->with('success', "Report status updated to {$request->status}.");
    }

    public function suspendProfile(Request $request, int $id)
    {
        $report = ProfileReport::findOrFail($id);

        if ($report->profile) {
            $report->profile->status = 'suspended';
            $report->profile->save();
        }

        $report->status = 'reviewed';
        $report->save();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'profile.suspend_from_report',
            'QRProfile',
            $report->profile_id,
            "Suspended profile #{$report->profile_id} from abuse report #{$report->id}",
            ['reason' => $report->reason],
            $request->ip()
        );

        return back()->with('success', 'Profile suspended successfully.');
    }
}
