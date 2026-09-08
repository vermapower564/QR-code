<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminDomainController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomDomain::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('domain', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $domains = $query->latest()->paginate(15);
        return view('admin.domains.index', compact('domains'));
    }

    public function verify(Request $request, int $id)
    {
        $domain = CustomDomain::findOrFail($id);
        
        // Attempt DNS record check for TXT verification
        $txtRecord = "_qr-verify.{$domain->domain}";
        $records = @dns_get_record($txtRecord, DNS_TXT);
        $verified = false;

        if ($records && is_array($records)) {
            foreach ($records as $record) {
                if (isset($record['txt']) && $record['txt'] === $domain->verification_token) {
                    $verified = true;
                    break;
                }
            }
        }

        if ($verified) {
            $domain->status = 'active';
            $domain->verified_at = now();
            $domain->save();

            AuditLogService::log(
                $request->user()?->id ?? 1,
                'domain.verified',
                'CustomDomain',
                $domain->id,
                "Custom domain {$domain->domain} successfully verified by admin",
                [],
                $request->ip()
            );

            return back()->with('success', "Domain {$domain->domain} successfully verified and activated!");
        }

        return back()->with('error', "DNS verification record not found for {$domain->domain}. Please ensure TXT record {$txtRecord} exists.");
    }

    public function toggleStatus(Request $request, int $id)
    {
        $domain = CustomDomain::findOrFail($id);
        $oldStatus = $domain->status;
        $newStatus = ($oldStatus === 'active') ? 'pending_dns' : 'active';
        $domain->status = $newStatus;
        $domain->save();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'domain.status_toggle',
            'CustomDomain',
            $domain->id,
            "Custom domain {$domain->domain} status updated to {$newStatus}",
            ['old_status' => $oldStatus, 'new_status' => $newStatus],
            $request->ip()
        );

        return back()->with('success', "Domain {$domain->domain} status updated to {$newStatus}.");
    }
}
