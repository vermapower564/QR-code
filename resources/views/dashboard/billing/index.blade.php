@extends('layouts.dashboard')

@section('title', 'Billing & Plans')

@section('content')
<div class="mb-8 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Current Active Subscription</span>
        <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $user->plan ? $user->plan->name : 'Free Starter Plan' }}</h2>
        <p class="text-xs text-slate-500 mt-0.5">Profile Limit: {{ $user->plan ? ($user->plan->profile_limit === -1 ? 'Unlimited' : $user->plan->profile_limit) : 1 }} profile(s)</p>
    </div>

    <span class="px-4 py-2 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
        <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Active Status
    </span>
</div>

<h3 class="text-lg font-bold text-slate-900 mb-6">Available Subscription Plans</h3>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    @foreach($plans as $plan)
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-slate-900 text-lg">{{ $plan->name }}</h4>
                <p class="text-2xl font-black text-slate-900 mt-2">${{ number_format($plan->price, 2) }} <span class="text-xs font-normal text-slate-500">/ {{ $plan->billing_cycle }}</span></p>

                <ul class="mt-4 space-y-2 text-xs text-slate-600">
                    <li><i class="fa-solid fa-check text-emerald-500 mr-1.5"></i> Profile Limit: {{ $plan->profile_limit === -1 ? 'Unlimited' : $plan->profile_limit }}</li>
                    <li><i class="fa-solid fa-check text-emerald-500 mr-1.5"></i> Link Limit: {{ $plan->link_limit === -1 ? 'Unlimited' : $plan->link_limit }}</li>
                </ul>
            </div>

            <form action="{{ route('dashboard.billing.subscribe') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}"/>
                <input type="hidden" name="provider" value="stripe"/>
                
                @if($user->plan_id == $plan->id)
                    <button type="button" disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed">Current Plan</button>
                @else
                    <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                        Upgrade to {{ $plan->name }}
                    </button>
                @endif
            </form>
        </div>
    @endforeach
</div>

<!-- Payment History -->
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900 text-base">Payment & Invoice History</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                <tr>
                    <th class="p-4">Transaction ID</th>
                    <th class="p-4">Provider</th>
                    <th class="p-4">Amount</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($payments as $payment)
                    <tr>
                        <td class="p-4 font-mono font-bold">{{ $payment->transaction_id }}</td>
                        <td class="p-4 uppercase font-semibold">{{ $payment->provider }}</td>
                        <td class="p-4 font-bold text-slate-900">${{ number_format($payment->amount, 2) }}</td>
                        <td class="p-4"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded-md">Completed</span></td>
                        <td class="p-4 text-slate-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">No payment history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
