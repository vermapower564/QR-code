@extends('layouts.public')

@section('title', 'Complete Your QR Profile - QR Identity')

@section('content')
<div class="min-h-[85vh] bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Progress Header -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold uppercase tracking-wider mb-3">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Step 2 of 2: Create Your First QR Profile
            </div>
            <h1 class="text-3xl font-black text-slate-900">Set Up Your Dynamic Identity</h1>
            <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">
                Fill in your profile details below. You can customize links, styling, and templates anytime from your dashboard.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-xs p-4 rounded-2xl">
                <p class="font-bold mb-1">Please fix the following issues:</p>
                <ul class="list-disc pl-4 space-y-1 font-semibold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200" x-data="{ submitting: false }">
            <form action="{{ route('onboarding.store') }}" method="POST" @submit="submitting = true" class="space-y-6">
                @csrf

                <!-- Basic Details -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-sky-600"></i> Profile Information
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $existingProfile->name ?? Auth::user()->name) }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username / Public Handle *</label>
                            <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                                <span class="bg-slate-100 text-slate-500 text-xs font-semibold px-3 flex items-center border-r border-slate-300">/p/</span>
                                <input type="text" name="username" value="{{ old('username', $existingProfile->slug ?? '') }}" required placeholder="john-doe" class="w-full px-3 py-3 text-sm outline-none"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Job Title / Designation</label>
                        <input type="text" name="designation" value="{{ old('designation', $existingProfile->designation ?? '') }}" placeholder="e.g. CEO & Founder" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Company / Organization</label>
                        <input type="text" name="company" value="{{ old('company', $existingProfile->company ?? Auth::user()->company) }}" placeholder="e.g. ABC Technologies" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bio / Brief Summary</label>
                    <textarea name="bio" rows="3" placeholder="Write a short summary about yourself or business..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition">{{ old('bio', $existingProfile->bio ?? '') }}</textarea>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-address-book text-sky-600"></i> Contact Information (for vCard)
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $existingProfile->phone ?? Auth::user()->phone) }}" placeholder="+1 999 999 9999" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $existingProfile->email ?? Auth::user()->email) }}" placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Website</label>
                            <input type="url" name="website" value="{{ old('website', $existingProfile->website ?? '') }}" placeholder="https://example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                        </div>
                    </div>
                </div>

                <!-- Template Choice -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-3 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-palette text-sky-600"></i> Select Template Preset
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($templates as $t)
                            <label class="border border-slate-200 rounded-2xl p-4 cursor-pointer hover:border-sky-500 transition relative flex flex-col justify-between">
                                <input type="radio" name="template_id" value="{{ $t->id }}" {{ (old('template_id', $existingProfile->template_id ?? '') == $t->id || $loop->first) ? 'checked' : '' }} class="absolute top-4 right-4 text-sky-600 focus:ring-sky-500"/>
                                <div>
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-sky-600 block mb-1">{{ $t->category }}</span>
                                    <h3 class="text-sm font-black text-slate-900">{{ $t->name }}</h3>
                                </div>
                                <span class="text-xs text-slate-400 mt-2 block">{{ $t->is_premium ? 'Premium Theme' : 'Standard Theme' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" :disabled="submitting" class="w-full py-4 px-6 font-black text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 rounded-2xl shadow-lg shadow-sky-500/25 transition text-base flex items-center justify-center gap-2">
                        <span x-show="!submitting"><i class="fa-solid fa-qrcode mr-1"></i> Save Profile & Generate Dynamic QR</span>
                        <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Generating Profile & QR Code...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
