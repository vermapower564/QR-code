@extends('layouts.public')

@section('title', $title . ' - QR Identity')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 prose prose-slate">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-6">{{ $title }}</h1>
        <p class="text-sm text-slate-500 mb-8">Last updated: {{ date('F j, Y') }}</p>

        <p class="text-slate-700 leading-relaxed mb-4">
            Welcome to QR Identity. By accessing our services, creating dynamic QR codes, or using our platform features, you agree to comply with our platform policies and terms of service.
        </p>

        <h3 class="text-xl font-bold text-slate-800 mt-6 mb-3">1. Use of Dynamic QR Codes & Content</h3>
        <p class="text-slate-700 leading-relaxed mb-4">
            Users retain full ownership of their profile data, custom links, and uploaded assets. Users agree not to submit malicious URLs, phishing links, or unauthorized content.
        </p>

        <h3 class="text-xl font-bold text-slate-800 mt-6 mb-3">2. Analytics & Data Privacy</h3>
        <p class="text-slate-700 leading-relaxed mb-4">
            We prioritize user privacy and GDPR compliance. IP addresses collected during QR code scans are hashed using SHA-256 and stored anonymously for aggregate device and geographic reporting.
        </p>
    </div>
</section>
@endsection
