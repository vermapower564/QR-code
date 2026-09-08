@extends('layouts.dashboard')

@section('title', 'Billing Overview - QR Identity')

@section('content')
<div class="space-y-8">
    <!-- Header & Sub-Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Billing & Subscriptions</h1>
            <p class="text-xs text-slate-500 mt-1">Manage your active plan, payment methods, and invoice history.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
            <a href="{{ route('dashboard.billing.index') }}" class="px-4 py-2 rounded-xl bg-white text-slate-900 shadow-sm">Overview</a>
            <a href="{{ route('dashboard.billing.invoices') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Invoices</a>
            <a href="{{ route('dashboard.billing.payment-methods') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Payment Methods</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Billing Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Current Plan Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Current Plan</span>
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900">{{ $user->plan ? $user->plan->name : 'Starter Free' }}</h3>
                <span class="px-2.5 py-1 rounded-full bg-sky-100 text-sky-700 text-[11px] font-extrabold uppercase">Monthly</span>
            </div>
            <p class="text-xs text-slate-500 mt-3">
                Renews on <span class="font-bold text-slate-800">{{ $currentSubscription ? $currentSubscription->ends_at->format('M d, Y') : now()->addMonth()->format('M d, Y') }}</span>
            </p>
        </div>

        <!-- Amount Due Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Amount Due</span>
            <div class="flex items-baseline gap-1">
                <h3 class="text-3xl font-black text-slate-900">${{ number_format($user->plan ? $user->plan->price : 0, 2) }}</h3>
                <span class="text-xs text-slate-500 font-bold">USD</span>
            </div>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Status: <span class="font-bold text-slate-800">Paid</span>
            </p>
        </div>

        <!-- Default Payment Method Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Default Payment Method</span>
            <div class="flex items-center gap-3">
                <div class="w-10 h-7 bg-slate-900 text-white rounded-lg flex items-center justify-center font-bold text-xs">
                    <i class="fa-brands fa-cc-visa text-base"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Visa •••• {{ $defaultPaymentMethod['last4'] }}</h4>
                    <span class="text-[11px] text-slate-500">Exp {{ $defaultPaymentMethod['exp_month'] }}/{{ $defaultPaymentMethod['exp_year'] }}</span>
                </div>
            </div>
            <a href="{{ route('dashboard.billing.payment-methods') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 block mt-3">Manage methods &rarr;</a>
        </div>

        <!-- Billing Status Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Billing Account Status</span>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                <i class="fa-solid fa-shield-check"></i> Account Active
            </div>
            <p class="text-xs text-slate-500 mt-3">No pending or failed payments.</p>
        </div>
    </div>

    <!-- 2. Recent Invoices Section -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Recent Invoices</h2>
                <p class="text-xs text-slate-500 mt-0.5">Your most recent payments and billing downloads.</p>
            </div>
            <a href="{{ route('dashboard.billing.invoices') }}" class="px-4 py-2 text-xs font-bold text-sky-600 bg-sky-50 rounded-xl hover:bg-sky-100 transition">
                View All Invoices &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100">
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 font-mono font-bold text-slate-900">INV-2026-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-4 text-slate-600">{{ $p->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-4 font-black text-slate-900">${{ number_format($p->amount, 2) }} {{ strtoupper($p->currency) }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 uppercase">{{ $p->status }}</span>
                            </td>
                            <td class="py-4 px-4 text-slate-600 capitalize"><i class="fa-solid fa-credit-card mr-1 text-slate-400"></i> {{ $p->provider }}</td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <a href="{{ route('dashboard.billing.invoices.download', $p->id) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-download mr-1"></i> Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 text-xs">
                                No billing invoices found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. Billing Activity Timeline -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-6">Recent Billing Activity</h2>

        <div class="space-y-4">
            @foreach($activityLog as $log)
                <div class="flex items-start gap-4 pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">{{ $log['event'] }}</h3>
                            <span class="text-xs text-slate-400">{{ $log['date'] }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $log['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Available Subscription Plans -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-2xl font-black text-slate-900">Subscription Plans</h2>
            <p class="text-xs text-slate-500 mt-1">Upgrade or modify your SaaS profile plan anytime.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="border border-slate-200 rounded-3xl p-6 flex flex-col justify-between hover:border-sky-500 transition relative">
                    @if($user->plan_id == $plan->id)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-sky-600 text-white text-[11px] font-extrabold uppercase px-3 py-0.5 rounded-full shadow">Current Active Plan</div>
                    @endif

                    <div>
                        <span class="text-xs font-extrabold uppercase text-sky-600 tracking-wider block mb-1">{{ $plan->billing_cycle }}</span>
                        <h3 class="text-xl font-black text-slate-900">{{ $plan->name }}</h3>
                        <div class="my-4 flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-900">${{ number_format($plan->price, 0) }}</span>
                            <span class="text-xs text-slate-500 font-bold">/month</span>
                        </div>
                        <ul class="text-xs space-y-2.5 text-slate-600 mb-6">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> {{ $plan->profile_limit == -1 ? 'Unlimited Profiles' : $plan->profile_limit . ' Profile' }}</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> {{ $plan->link_limit == -1 ? 'Unlimited Links' : $plan->link_limit . ' Custom Links' }}</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Dynamic QR & vCard Card</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Scan Analytics & Tracking</li>
                        </ul>
                    </div>

                    <form action="{{ route('billing.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="provider" value="stripe">
                        <button type="submit" {{ $user->plan_id == $plan->id ? 'disabled' : '' }} class="w-full py-3 px-4 font-bold text-xs rounded-xl shadow transition {{ $user->plan_id == $plan->id ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-900 hover:bg-slate-800 text-white' }}">
                            {{ $user->plan_id == $plan->id ? 'Current Plan' : 'Select Plan' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
