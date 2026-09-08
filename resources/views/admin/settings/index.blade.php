@extends('layouts.admin')

@section('title', 'Global SaaS Settings')

@section('content')
<div class="max-w-2xl bg-slate-950 p-8 rounded-2xl border border-slate-800">
    <h2 class="text-xl font-bold text-white mb-6 border-b border-slate-800 pb-4">Global SaaS Platform Settings</h2>

    <div class="space-y-6 text-xs text-slate-300">
        <div>
            <label class="block font-bold text-slate-400 mb-1">Platform Brand Name</label>
            <input type="text" value="QR Identity SaaS" readonly class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl font-semibold outline-none"/>
        </div>

        <div>
            <label class="block font-bold text-slate-400 mb-1">Default Free Plan Profiles Limit</label>
            <input type="text" value="1 Profile" readonly class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl font-semibold outline-none"/>
        </div>

        <div>
            <label class="block font-bold text-slate-400 mb-1">Active Payment Drivers</label>
            <input type="text" value="Stripe + Razorpay (Server-Side Webhook Managed)" readonly class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl font-semibold outline-none"/>
        </div>
    </div>
</div>
@endsection
