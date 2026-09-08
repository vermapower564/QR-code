@extends('layouts.public')

@section('title', 'Contact Support - QR Identity')

@section('content')
<div class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8" x-data="{ submitting: false, presetSubject: '' }">
    <div class="max-w-7xl mx-auto space-y-12">
        <!-- 1. Contact Hero -->
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold uppercase tracking-wider mb-4">
                <i class="fa-solid fa-headset"></i> Support & Guidance
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">Contact Support</h1>
            <p class="mt-3 text-base text-slate-600 leading-relaxed">
                Have a question about your account, QR profile, analytics, or billing? We're here to help.
            </p>
        </div>

        <!-- 2. Two-Column Layout: Left Support Categories, Right Contact Form -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Column: 3 Support Categories (5 cols on lg) -->
            <div class="lg:col-span-5 space-y-4">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2 px-1">How can we assist you?</h2>

                <!-- Category 1: Account & Profile Support -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-600 font-bold text-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">Account & Profile Support</h3>
                            <p class="text-xs text-slate-500 mt-1">Need help creating or managing your QR profile?</p>
                            
                            <div class="flex flex-wrap gap-1.5 mt-3 mb-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Account creation</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Profile editing</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Social links</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Custom links</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Profile customization</span>
                            </div>

                            <button type="button" @click="presetSubject = 'Account & Profile Support'; $refs.subjectInput.value = 'Account & Profile Support'; $refs.formSection.scrollIntoView({ behavior: 'smooth' })" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1.5">
                                <span>Get Account Help</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category 2: QR & Technical Support -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 font-bold text-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">QR & Technical Support</h3>
                            <p class="text-xs text-slate-500 mt-1">Having trouble with your QR code or public profile?</p>
                            
                            <div class="flex flex-wrap gap-1.5 mt-3 mb-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">QR generation</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">QR download</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Public profile</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">QR scanning</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Profile URL</span>
                            </div>

                            <button type="button" @click="presetSubject = 'QR & Technical Support'; $refs.subjectInput.value = 'QR & Technical Support'; $refs.formSection.scrollIntoView({ behavior: 'smooth' })" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1.5">
                                <span>Get Technical Help</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category 3: Billing & Subscription -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 font-bold text-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900">Billing & Subscription</h3>
                            <p class="text-xs text-slate-500 mt-1">Questions about your plan or payments?</p>
                            
                            <div class="flex flex-wrap gap-1.5 mt-3 mb-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Free plan</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Pro plan</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Business plan</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Subscription</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Payments</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">Invoices</span>
                            </div>

                            <button type="button" @click="presetSubject = 'Billing & Subscription'; $refs.subjectInput.value = 'Billing & Subscription'; $refs.formSection.scrollIntoView({ behavior: 'smooth' })" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1.5">
                                <span>Get Billing Help</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact Form (7 cols on lg) -->
            <div class="lg:col-span-7" x-ref="formSection">
                <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-900">Send us a message</h2>
                        <p class="text-xs text-slate-500 mt-1">Fill in the details below and our team will get back to you shortly.</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold">
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" @submit="submitting = true" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Name *</label>
                                <input type="text" name="name" value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subject *</label>
                            <input type="text" name="subject" x-ref="subjectInput" value="{{ old('subject') }}" required placeholder="Select a topic above or type a subject..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Message *</label>
                            <textarea name="message" rows="5" required placeholder="Describe your question or issue in detail..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" :disabled="submitting" class="w-full py-4 px-6 font-bold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 rounded-2xl shadow-md shadow-sky-500/20 transition text-sm flex items-center justify-center gap-2">
                            <span x-show="!submitting"><i class="fa-solid fa-paper-plane mr-1"></i> Send Message</span>
                            <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Sending...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. Small Common Questions / FAQ Section -->
        <div class="pt-8 border-t border-slate-200">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-slate-900">Common Questions</h2>
                <p class="text-xs text-slate-500 mt-1">Quick answers to frequently asked questions about QR Identity</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-sky-600"></i> How do I create my dynamic QR profile?
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Simply sign up for a free account, enter your profile details, customize your template and links, and click generate. Your dynamic QR code is created instantly.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-sky-600"></i> Can I update links without changing my QR code?
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Yes! Dynamic QR codes encode your unique profile URL (`/p/username`). You can update your social networks, custom buttons, or contact details anytime without re-printing your QR code.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-sky-600"></i> What formats can I download my QR code in?
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        You can download high-resolution PNG images (512px to 2048px), vector SVG graphics for crisp printing, or branded PDF frame documents directly from your dashboard.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-sky-600"></i> How does dynamic QR scanning analytics work?
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Every scan is tracked anonymously using SHA-256 IP hashing for privacy compliance. You can view total scans, unique visitors, link click-through rates, and country breakdown in real-time.
                    </p>
                </div>
            </div>
        </div>

        <!-- 4. Final CTA -->
        <div class="bg-gradient-to-r from-sky-600 to-indigo-700 rounded-3xl p-8 sm:p-10 text-white text-center shadow-xl">
            <h2 class="text-2xl sm:text-3xl font-black mb-2">Ready to create your digital identity?</h2>
            <p class="text-sm text-sky-100 max-w-xl mx-auto mb-6">
                Join thousands of creators, entrepreneurs, and teams sharing their digital identity with dynamic QR codes.
            </p>
            <a href="{{ Auth::check() ? route('dashboard.profiles.create') : route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-slate-900 font-bold rounded-2xl shadow-lg hover:bg-slate-100 transition text-sm">
                <span>Create Your QR</span> <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection
