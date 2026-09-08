<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use App\Services\FeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    public function __construct(
        protected FeatureService $featureService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $hasFeature = $this->featureService->canUseFeature($user, 'custom_domain');

        $domains = CustomDomain::where('user_id', $user->id)->latest()->paginate(15)->withQueryString();

        return view('dashboard.domains.index', compact('domains', 'hasFeature'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$this->featureService->canUseFeature($user, 'custom_domain')) {
            return back()->with('error', 'Your current subscription plan does not include custom domains. Please upgrade to Business plan.');
        }

        $request->validate([
            'domain' => 'required|string|max:255|unique:custom_domains,domain',
        ]);

        $domainStr = strtolower(trim($request->domain));
        // Remove protocols or trailing slashes if user entered full URL
        $domainStr = preg_replace('/^https?:\/\//', '', $domainStr);
        $domainStr = rtrim($domainStr, '/');

        $verificationToken = 'qr-verify-' . Str::random(32);

        CustomDomain::create([
            'user_id' => $user->id,
            'domain' => $domainStr,
            'verification_token' => $verificationToken,
            'status' => 'pending_dns',
            'ssl_status' => 'pending',
        ]);

        return back()->with('success', "Domain {$domainStr} added successfully! Please add the DNS TXT record to verify ownership.");
    }

    public function verify(Request $request, int $id)
    {
        $user = $request->user();
        $domain = CustomDomain::where('user_id', $user->id)->findOrFail($id);

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
            $domain->ssl_status = 'active';
            $domain->verified_at = now();
            $domain->save();

            return back()->with('success', "Domain {$domain->domain} successfully verified and activated!");
        }

        return back()->with('error', "Verification TXT record not found for {$domain->domain}. Please ensure TXT record {$txtRecord} contains value: {$domain->verification_token}");
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $domain = CustomDomain::where('user_id', $user->id)->findOrFail($id);
        $domainName = $domain->domain;
        $domain->delete();

        return back()->with('success', "Domain {$domainName} removed successfully.");
    }
}
