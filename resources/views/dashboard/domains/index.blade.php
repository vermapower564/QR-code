@extends('layouts.dashboard')

@section('title', 'Custom Domains')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Custom Domains</h1>
            <p class="text-xs text-slate-500 mt-1">Connect custom domain names to your dynamic QR profiles.</p>
        </div>
    </div>

    @if(!$hasFeature)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 p-6 rounded-3xl space-y-3">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-crown text-amber-600 text-xl"></i>
                <h3 class="text-base font-bold">Business Plan Required</h3>
            </div>
            <p class="text-xs text-amber-700 leading-relaxed">
                Custom domain mapping is available exclusively on the Business Plan. Upgrade your subscription to connect custom domains like <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono">card.yourdomain.com</code>.
            </p>
            <a href="{{ route('dashboard.billing.index') }}" class="inline-block px-4 py-2 bg-amber-600 text-white font-bold text-xs rounded-xl shadow">Upgrade to Business</a>
        </div>
    @else
        <!-- Add Domain Form -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-2">Connect New Custom Domain</h3>
            <p class="text-xs text-slate-500 mb-4">Enter your domain or subdomain (e.g. <span class="font-mono font-semibold">qr.yourbrand.com</span>).</p>

            <form action="{{ route('dashboard.domains.store') }}" method="POST" class="flex flex-wrap sm:flex-nowrap gap-3">
                @csrf
                <input type="text" name="domain" required placeholder="qr.yourbrand.com" class="w-full p-3 rounded-xl border border-slate-300 text-sm outline-none"/>
                <button type="submit" class="px-6 py-3 bg-sky-600 text-white font-bold text-sm rounded-xl shadow shrink-0 hover:bg-sky-700">Add Domain</button>
            </form>
        </div>

        <!-- Custom Domains List -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4">Your Custom Domains</h3>

            @if($domains->count() > 0)
                <div class="space-y-4">
                    @foreach($domains as $domain)
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3">
                                    <span class="text-base font-black text-slate-900">{{ $domain->domain }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $domain->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $domain->status }}
                                    </span>
                                </div>
                                @if($domain->status !== 'active')
                                    <div class="mt-2 text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200">
                                        <p class="font-bold text-slate-800 mb-1">Required DNS TXT Record:</p>
                                        <div class="font-mono text-[11px] space-y-0.5 text-slate-700">
                                            <p>Type: <span class="font-bold text-sky-600">TXT</span></p>
                                            <p>Host/Name: <span class="font-bold text-sky-600">_qr-verify.{{ $domain->domain }}</span></p>
                                            <p>Value: <span class="font-bold text-sky-600">{{ $domain->verification_token }}</span></p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if($domain->status !== 'active')
                                    <form action="{{ route('dashboard.domains.verify', $domain->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow">Verify DNS</button>
                                    </form>
                                @endif
                                <form action="{{ route('dashboard.domains.destroy', $domain->id) }}" method="POST" onsubmit="return confirm('Remove custom domain?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 hover:bg-rose-100">Remove</button>
                                </form>
                            </div>
                        </div>
                </div>
                <div class="mt-4">
                    {{ $domains->links('vendor.pagination.custom') }}
                </div>
            @else
                <p class="text-xs text-slate-500 text-center py-6">No custom domains connected yet.</p>
            @endif
        </div>
    @endif
</div>
@endsection
