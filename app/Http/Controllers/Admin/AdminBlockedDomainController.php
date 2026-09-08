<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDomain;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminBlockedDomainController extends Controller
{
    public function index(Request $request)
    {
        $domains = BlockedDomain::with('creator')->latest()->paginate(15);
        return view('admin.blocked_domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255|unique:blocked_domains,domain',
            'reason' => 'nullable|string|max:500',
        ]);

        $domainStr = strtolower(trim($request->domain));
        $domainStr = preg_replace('/^https?:\/\//', '', $domainStr);
        $domainStr = rtrim($domainStr, '/');

        $blocked = BlockedDomain::create([
            'domain' => $domainStr,
            'reason' => $request->reason ?? 'Malicious domain',
            'status' => 'active',
            'created_by' => $request->user()?->id ?? 1,
        ]);

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'domain.block',
            'BlockedDomain',
            $blocked->id,
            "Added domain {$domainStr} to blocklist",
            ['reason' => $blocked->reason],
            $request->ip()
        );

        return back()->with('success', "Domain {$domainStr} added to blocklist.");
    }

    public function destroy(Request $request, int $id)
    {
        $blocked = BlockedDomain::findOrFail($id);
        $domainStr = $blocked->domain;
        $blocked->delete();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'domain.unblock',
            'BlockedDomain',
            $id,
            "Removed domain {$domainStr} from blocklist",
            [],
            $request->ip()
        );

        return back()->with('success', "Domain {$domainStr} removed from blocklist.");
    }
}
