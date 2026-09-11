@extends('layouts.public')

@section('title', 'How It Works - QR Identity')

@section('content')
<section class="py-20 bg-slate-50 min-h-[80vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">How It Works</h1>
            <p class="mt-4 text-lg text-slate-600">Get your dynamic QR profile running in under 2 minutes. Follow these simple steps.</p>
        </div>

        <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">

            <!-- Step 1 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-sky-500 text-white font-bold shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm z-10">
                    1
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Create Your Profile</h3>
                    <p class="text-sm text-slate-600">Sign up and choose a unique username. This will be your permanent digital identity link (e.g. yourdomain.com/p/johndoe).</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-sky-500 text-white font-bold shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm z-10">
                    2
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Add Contact & Social Links</h3>
                    <p class="text-sm text-slate-600">Fill in your website, contact details, and optional social links like Instagram or LinkedIn. Everything is organized in one beautiful landing page.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-sky-500 text-white font-bold shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm z-10">
                    3
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Get Your Unique QR Code</h3>
                    <p class="text-sm text-slate-600">Once your profile is ready, a dynamic QR code is automatically generated. It encodes exactly one thing: your permanent profile URL.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-sky-500 text-white font-bold shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm z-10">
                    4
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Share or Print</h3>
                    <p class="text-sm text-slate-600">Download your QR code in high quality and print it on business cards, flyers, menus, or share the link directly online.</p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-sky-500 text-white font-bold shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm z-10">
                    5
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Someone Scans the Code</h3>
                    <p class="text-sm text-slate-600">When someone scans your QR code with their phone camera, your digital profile instantly opens on their device.</p>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-indigo-500 text-white font-bold shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-sm z-10">
                    <i class="fa-solid fa-infinity"></i>
                </div>
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-indigo-50 p-6 rounded-2xl border border-indigo-200 shadow-sm">
                    <h3 class="text-lg font-bold text-indigo-900 mb-2">Edit Anytime, Keep the QR</h3>
                    <p class="text-sm text-indigo-700">If you later change your phone number or add a new Instagram, just update your profile in the dashboard. The same printed QR code continues to work perfectly!</p>
                </div>
            </div>

        </div>

        <!-- Navigation Buttons -->
        <div class="mt-16 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative z-20">
            <a href="{{ route('pricing') }}" class="px-6 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back: Pricing
            </a>
            
            <a href="{{ route('register') }}" class="px-6 py-3 bg-sky-600 text-white hover:bg-sky-700 rounded-xl font-bold text-sm transition flex items-center gap-2 shadow-md">
                Get Started <i class="fa-solid fa-check"></i>
            </a>
        </div>
    </div>
</section>
@endsection
