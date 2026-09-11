@extends('layouts.public')

@section('title', 'Features - QR Identity')

@section('content')
<section class="py-20 bg-slate-50 min-h-[80vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">Powerful Features, Simple Setup</h1>
            <p class="mt-4 text-lg text-slate-600">Everything you need to manage your dynamic digital identity with ease. No technical skills required.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Dynamic QR Feature -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition">
                <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-xl flex items-center justify-center text-xl mb-6">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Dynamic QR Codes</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Your QR code points to one permanent profile URL. You can print it once and update your profile information later. The QR code never needs to be regenerated!
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-xl text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5"></i> Example: Change your job or website later, and your printed business cards still work seamlessly.
                </div>
            </div>

            <!-- Social Links Feature -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition">
                <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center text-xl mb-6">
                    <i class="fa-solid fa-hashtag"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Optional Social Links</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Easily add your Instagram, Facebook, LinkedIn, YouTube, and WhatsApp to your profile. Visitors can connect with you instantly on their preferred platform.
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-xl text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5"></i> Example: A freelancer adds their LinkedIn and portfolio site so clients can view their work immediately.
                </div>
            </div>

            <!-- Single Profile Feature -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl mb-6">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Unified Profile Identity</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Create a beautiful, mobile-friendly landing page that houses all your important contact details, bio, and custom buttons in one place.
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-xl text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5"></i> Example: Instead of sharing 5 different links, share one QR code that holds everything securely.
                </div>
            </div>

            <!-- Edit Profile Feature -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center text-xl mb-6">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Live Editing & Updates</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Log in anytime to change your profile picture, bio, or social links. Changes are instantly visible to anyone who scans your existing QR code.
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-xl text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5"></i> Example: A business updates their Instagram handle. They just edit it in the dashboard, without throwing away printed menus.
                </div>
            </div>

            <!-- Contact Save Feature -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-xl mb-6">
                    <i class="fa-solid fa-address-book"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Save to Contacts</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Visitors can view your contact information and download it directly to their phone's address book with a single tap.
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-xl text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5"></i> Example: Met someone at a conference? They scan your code and tap "Save Contact" to instantly store your number.
                </div>
            </div>

            <!-- VCF Support Feature -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition">
                <div class="w-12 h-12 bg-violet-100 text-violet-600 rounded-xl flex items-center justify-center text-xl mb-6">
                    <i class="fa-solid fa-file-arrow-down"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">High-Quality Downloads</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Download your dynamic QR code in various formats like PNG, SVG, or PDF, ensuring crisp, professional quality for any print material.
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-xl text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5"></i> Example: Download the SVG version and hand it over to your graphic designer for printing on billboards.
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-16 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <a href="{{ route('home') }}" class="px-6 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Home
            </a>
            
            <a href="{{ route('pricing') }}" class="px-6 py-3 bg-sky-600 text-white hover:bg-sky-700 rounded-xl font-bold text-sm transition flex items-center gap-2 shadow-md">
                Next: Pricing <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
