@extends('layouts.admin')

@section('title', 'Subscription Plans')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Existing Plans List -->
    <div class="lg:col-span-2 space-y-4">
        <h2 class="text-xl font-bold text-white mb-4">Configured Subscription Plans</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($plans as $plan)
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-white text-lg">{{ $plan->name }}</h3>
                        <p class="text-2xl font-black text-emerald-400 mt-2">${{ number_format($plan->price, 2) }} <span class="text-xs text-slate-400">/ {{ $plan->billing_cycle }}</span></p>
                        <p class="text-xs font-mono text-slate-500 mt-1">Slug: {{ $plan->slug }}</p>

                        <ul class="mt-4 space-y-1.5 text-xs text-slate-400">
                            <li>Profile Limit: <strong class="text-white">{{ $plan->profile_limit === -1 ? 'Unlimited' : $plan->profile_limit }}</strong></li>
                            <li>Link Limit: <strong class="text-white">{{ $plan->link_limit === -1 ? 'Unlimited' : $plan->link_limit }}</strong></li>
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Create Plan Form -->
    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
        <h3 class="font-bold text-white text-base mb-4">Create New Plan</h3>

        <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">Plan Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">Slug</label>
                <input type="text" name="slug" required placeholder="pro" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Price ($)</label>
                    <input type="number" step="0.01" name="price" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Currency</label>
                    <input type="text" name="currency" value="USD" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Profile Limit (-1 unl)</label>
                    <input type="number" name="profile_limit" value="1" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Link Limit</label>
                    <input type="number" name="link_limit" value="10" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">Billing Cycle</label>
                <select name="billing_cycle" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 font-bold text-xs text-white rounded-xl transition">Create Plan</button>
        </form>
    </div>
</div>
@endsection
