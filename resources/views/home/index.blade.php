@extends('layouts.public')

@section('title', 'Dynamic QR Social Profile SaaS - Create Your Digital Identity')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-20 pb-24 bg-gradient-to-b from-sky-50/50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold uppercase tracking-wider mb-8">
            <i class="fa-solid fa-bolt"></i> Dynamic QR Code Generator & Social Profile SaaS
        </div>

        <h1 class="text-4xl sm:text-6xl font-black text-slate-900 tracking-tight max-w-4xl mx-auto leading-tight">
            Create your digital identity. <br class="hidden sm:inline"/>
            <span class="bg-gradient-to-r from-sky-600 to-indigo-600 bg-clip-text text-transparent">One QR code</span> for all your links.
        </h1>

        <p class="mt-6 text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto font-normal">
            Connect your website, Instagram, LinkedIn, WhatsApp, and contact card under a single dynamic QR code. Update your profile links anytime without reprinting your QR.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-2xl shadow-lg shadow-sky-500/20 transition">
                Create Your QR <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
            </a>
            <a href="{{ route('how-it-works') }}" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-2xl transition">
                See How It Works
            </a>
        </div>

        <!-- Interactive Preview Mockup -->
        <div id="demo" class="mt-16 max-w-4xl mx-auto bg-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-slate-200/80 flex flex-col md:flex-row items-center gap-10">
            <!-- QR Card -->
            <div class="bg-slate-900 text-white p-8 rounded-2xl flex flex-col items-center text-center shadow-xl w-full md:w-80">
                <div class="bg-white p-4 rounded-2xl mb-4 shadow-md">
                    <!-- Sample QR SVG -->
                    <svg class="w-44 h-44" viewBox="0 0 100 100">
                        <rect width="100" height="100" fill="#ffffff"/>
                        <rect x="10" y="10" width="80" height="80" fill="none" stroke="#0f172a" stroke-width="4"/>
                        <rect x="20" y="20" width="20" height="20" fill="#0f172a"/>
                        <rect x="60" y="20" width="20" height="20" fill="#0f172a"/>
                        <rect x="20" y="60" width="20" height="20" fill="#0f172a"/>
                        <circle cx="50" cy="50" r="10" fill="#0284c7"/>
                    </svg>
                </div>
                <p class="font-bold text-lg text-white">John Doe</p>
                <p class="text-xs text-sky-400 font-medium">CEO @ TechCorp</p>
                <span class="mt-4 text-[10px] uppercase font-bold tracking-widest text-slate-400 bg-slate-800 px-3 py-1 rounded-full">SCAN TO CONNECT</span>
            </div>

            <!-- Profile Landing Mockup -->
            <div class="flex-1 w-full bg-slate-50 border border-slate-200 rounded-2xl p-6 text-left">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-sky-600 text-white flex items-center justify-center font-bold text-xl">
                        JD
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900">John Doe</h3>
                        <p class="text-xs text-slate-500">Technology Entrepreneur & Speaker</p>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center gap-3 font-semibold text-sm text-slate-800 shadow-sm">
                        <i class="fa-globe text-sky-600 w-5"></i> Official Website
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center gap-3 font-semibold text-sm text-slate-800 shadow-sm">
                        <i class="fa-brands fa-linkedin text-blue-700 w-5"></i> LinkedIn Profile
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center gap-3 font-semibold text-sm text-slate-800 shadow-sm">
                        <i class="fa-brands fa-instagram text-pink-600 w-5"></i> Instagram Portfolio
                    </div>
                    <div class="bg-sky-600 text-white p-3.5 rounded-xl font-bold text-sm text-center shadow-md">
                        <i class="fa-solid fa-address-book mr-2"></i> Save Contact (.vcf)
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-20 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900">How It Works</h2>
        <p class="mt-3 text-slate-600">Get your dynamic profile active in under 2 minutes.</p>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">1</div>
                <h3 class="font-bold text-slate-900 mb-2">Create Profile</h3>
                <p class="text-sm text-slate-600">Enter your contact details, bio, and custom profile links.</p>
            </div>
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">2</div>
                <h3 class="font-bold text-slate-900 mb-2">Generate QR</h3>
                <p class="text-sm text-slate-600">System generates a dynamic vector QR code pointing to your slug.</p>
            </div>
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">3</div>
                <h3 class="font-bold text-slate-900 mb-2">Download & Print</h3>
                <p class="text-sm text-slate-600">Export high-resolution PNG, SVG, or print-ready PDF frames.</p>
            </div>
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">4</div>
                <h3 class="font-bold text-slate-900 mb-2">Track Scans</h3>
                <p class="text-sm text-slate-600">Get real-time scan metrics, link clicks, device, and location analytics.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900">Simple, Transparent Pricing</h2>
        <p class="mt-3 text-slate-600">Start free and scale as your network grows.</p>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
            <!-- Free Plan -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Starter Free</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">$0 <span class="text-sm font-normal text-slate-500">/ forever</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> 1 Dynamic QR Profile</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Standard Social Links</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Basic QR Customization</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> PNG Download</li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3 px-4 font-bold text-sky-600 border border-sky-600 rounded-xl hover:bg-sky-50 transition">Get Started</a>
            </div>

            <!-- Pro Plan -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl border border-slate-800 shadow-xl flex flex-col justify-between relative">
                <span class="absolute -top-3.5 right-8 bg-sky-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">POPULAR</span>
                <div>
                    <h3 class="text-xl font-bold">Pro Creator</h3>
                    <p class="text-3xl font-extrabold mt-4">$9 <span class="text-sm font-normal text-slate-400">/ month</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-300">
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Unlimited Profiles</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Remove Branding</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Vector SVG & PDF Frames</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Advanced Real-time Analytics</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> VCF Contact Save Button</li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3 px-4 font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-500 shadow-lg transition">Start Free Trial</a>
            </div>

            <!-- Business Plan -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Business & Teams</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">$29 <span class="text-sm font-normal text-slate-500">/ month</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Everything in Pro</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Bulk CSV QR Generation</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> REST API Access</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Outbound Webhooks</li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3 px-4 font-bold text-slate-900 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Contact Sales</a>
            </div>
        </div>
    </div>
</section>
@endsection
