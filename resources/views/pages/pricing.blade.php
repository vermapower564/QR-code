@extends('layouts.public')

@section('title', 'Pricing Plans - QR Identity')

@section('content')
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-extrabold text-slate-900">Simple, Transparent Pricing</h1>
        <p class="mt-3 text-slate-600">Choose the perfect plan for your personal or business dynamic QR profile.</p>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
            @foreach($plans as $plan)
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">{{ $plan->name }}</h3>
                        <p class="text-3xl font-black text-slate-900 mt-4">${{ number_format($plan->price, 2) }} <span class="text-sm font-normal text-slate-500">/ {{ $plan->billing_cycle }}</span></p>
                        <ul class="mt-6 space-y-3 text-sm text-slate-600">
                            <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> {{ $plan->profile_limit === -1 ? 'Unlimited' : $plan->profile_limit }} Dynamic QR Profile(s)</li>
                            <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> {{ $plan->link_limit === -1 ? 'Unlimited' : $plan->link_limit }} Custom Links</li>
                            <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> PNG, SVG & PDF Downloads</li>
                            <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> VCF Contact Save Button</li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition">Get Started</a>
                </div>
            @endforeach
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-16 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <a href="{{ route('features') }}" class="px-6 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back: Features
            </a>
            
            <a href="{{ route('how-it-works') }}" class="px-6 py-3 bg-sky-600 text-white hover:bg-sky-700 rounded-xl font-bold text-sm transition flex items-center gap-2 shadow-md">
                Next: How It Works <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
