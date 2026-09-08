@extends('layouts.dashboard')

@section('title', 'Create QR Profile')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl border border-slate-200 shadow-sm"
     x-data="{ 
         step: 1,
         totalSteps: 3,
         submitting: false,
         name: '{{ old('name') }}',
         username: '{{ old('username') }}',
         bio: '{{ old('bio') }}',
         phone: '{{ old('phone') }}',
         email: '{{ old('email') }}',
         website: '{{ old('website') }}',
         designation: '{{ old('designation') }}',
         company: '{{ old('company') }}',
         bioMax: 500,
         canGoNext() {
             if (this.step === 1) return this.name.trim() !== '' && this.username.trim() !== '';
             if (this.step === 2 && this.phone && this.phone.length !== 10) return false;
             return true;
         },
         nextStep() {
             if (this.canGoNext() && this.step < this.totalSteps) {
                 this.step++;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             }
         },
         prevStep() {
             if (this.step > 1) {
                 this.step--;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             }
         }
     }">

    <div class="mb-6 border-b border-slate-100 pb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Create New Dynamic Profile</h2>
            <p class="text-xs text-slate-500 mt-0.5">Enter your details to generate your unique public URL & QR code.</p>
        </div>
        <div class="text-xs font-bold text-sky-600 bg-sky-50 px-3 py-1.5 rounded-full">
            Step <span x-text="step"></span> of <span x-text="totalSteps"></span>
        </div>
    </div>

    <!-- Step Progress Bar -->
    <div class="w-full bg-slate-100 h-2 rounded-full mb-8 overflow-hidden">
        <div class="bg-sky-600 h-full transition-all duration-300" :style="'width: ' + (step / totalSteps * 100) + '%'"></div>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-sm p-4 rounded-xl">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.profiles.store') }}" method="POST" enctype="multipart/form-data" @submit="submitting = true" class="space-y-6">
        @csrf

        <!-- STEP 1: Basic Information -->
        <div x-show="step === 1" x-transition.opacity>
            <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-sky-600"></i> Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name *</label>
                    <input type="text" name="name" x-model="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username / Slug *</label>
                    <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                        <span class="bg-slate-100 px-3 py-3 text-slate-500 text-xs font-mono border-r border-slate-300 flex items-center">/p/</span>
                        <input type="text" name="username" x-model="username" value="{{ old('username') }}" placeholder="john-doe" required class="w-full px-3 py-3 text-sm outline-none border-none"/>
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Public URL: https://yourdomain.com/p/<span x-text="username || 'your-handle'"></span></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Designation / Title</label>
                    <input type="text" name="designation" x-model="designation" value="{{ old('designation') }}" placeholder="e.g. CEO & Founder" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company / Organization</label>
                    <input type="text" name="company" x-model="company" value="{{ old('company') }}" placeholder="e.g. ABC Technologies" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Bio / Description</label>
                    <span class="text-[11px] font-semibold text-slate-400"><span x-text="bio.length"></span> / <span x-text="bioMax"></span> chars</span>
                </div>
                <textarea name="bio" x-model="bio" maxlength="500" rows="3" placeholder="Technology entrepreneur, software engineer, speaker..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition">{{ old('bio') }}</textarea>
            </div>
        </div>

        <!-- STEP 2: Contact & Media -->
        <div x-show="step === 2" x-transition.opacity style="display: none;">
            <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-address-book text-sky-600"></i> Contact Information & Media
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" name="phone" x-model="phone" @input="phone = phone.replace(/[^0-9]/g, '').slice(0, 10)" maxlength="10" pattern="[0-9]{10}" placeholder="9876543210" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition" :class="{'border-rose-400 focus:ring-rose-500': phone && phone.length !== 10}"/>
                    <p x-show="phone && phone.length !== 10" class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> Mobile number must be strictly 10 digits (0-9).
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Public Contact Email</label>
                    <input type="email" name="email" x-model="email" value="{{ old('email') }}" placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Website URL</label>
                    <input type="url" name="website" x-model="website" value="{{ old('website') }}" placeholder="https://example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Profile Avatar Image</label>
                    <input type="file" name="profile_image" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100"/>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company Logo</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"/>
                </div>
            </div>
        </div>

        <!-- STEP 3: Preview & Submit -->
        <div x-show="step === 3" x-transition.opacity style="display: none;">
            <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-eye text-sky-600"></i> Profile Preview & Submit
            </h3>

            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 text-left mb-6 max-w-md mx-auto">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-xl flex items-center justify-center">
                        <span x-text="name ? name.charAt(0).toUpperCase() : 'J'"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base" x-text="name || 'John Doe'"></h4>
                        <p class="text-xs text-slate-500" x-text="designation || 'CEO & Founder'"></p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 mb-4" x-text="bio || 'Technology entrepreneur, speaker, software developer...'"></p>
                <div class="space-y-2 text-xs font-semibold text-sky-700">
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200" x-text="'/p/' + (username || 'john-doe')"></div>
                </div>
            </div>
        </div>

        <!-- Back / Next Navigation Controls -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
            <button type="button" @click="prevStep()" :disabled="step === 1 || submitting" class="px-6 py-3 font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 disabled:opacity-40 rounded-xl transition text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back
            </button>

            <template x-if="step < totalSteps">
                <button type="button" @click="nextStep()" :disabled="!canGoNext() || submitting" class="px-6 py-3 font-bold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-40 rounded-xl shadow-md transition text-sm flex items-center gap-2">
                    Next <i class="fa-solid fa-arrow-right"></i>
                </button>
            </template>

            <template x-if="step === totalSteps">
                <button type="submit" :disabled="submitting" class="px-6 py-3 font-black text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 rounded-xl shadow-lg transition text-sm flex items-center gap-2">
                    <span x-show="!submitting"><i class="fa-solid fa-qrcode mr-1"></i> Create QR Profile</span>
                    <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Creating Profile & QR Code...</span>
                </button>
            </template>
        </div>
    </form>
</div>
@endsection
