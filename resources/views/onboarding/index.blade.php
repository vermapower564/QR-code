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

        <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200" 
             x-data="{ 
                 step: parseInt(new URLSearchParams(window.location.search).get('step')) || 1, 
                 totalSteps: 4, 
                 submitting: false,
                 name: '{{ old('name', $existingProfile->name ?? Auth::user()->name) }}',
                 username: '{{ old('username', $existingProfile->slug ?? '') }}',
                 phone: '{{ old('phone', $existingProfile->phone ?? Auth::user()->phone) }}',
                 init() {
                     window.addEventListener('popstate', () => {
                         this.step = parseInt(new URLSearchParams(window.location.search).get('step')) || 1;
                     });
                 },
                 canGoNext() {
                     if (this.step === 1) return this.name.trim() !== '' && this.username.trim() !== '';
                     if (this.step === 3 && this.phone && this.phone.length !== 10) return false;
                     return true;
                 },
                 nextStep() {
                     if (this.canGoNext() && this.step < this.totalSteps) {
                         this.step++;
                         const url = new URL(window.location);
                         url.searchParams.set('step', this.step);
                         window.history.pushState({ step: this.step }, '', url);
                         window.scrollTo({ top: 0, behavior: 'smooth' });
                     }
                 },
                 prevStep() {
                     if (this.step > 1) {
                         this.step--;
                         const url = new URL(window.location);
                         url.searchParams.set('step', this.step);
                         window.history.pushState({ step: this.step }, '', url);
                         window.scrollTo({ top: 0, behavior: 'smooth' });
                     }
                 }
             }">

            <!-- Multi-step Indicator Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                    <span>Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
                    <span x-text="step === 1 ? 'Basic Info' : (step === 2 ? 'Professional' : (step === 3 ? 'Contact Info' : 'Template & Submit'))"></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-sky-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + (step / totalSteps * 100) + '%'"></div>
                </div>
            </div>

            <form action="{{ route('onboarding.store') }}" method="POST" @submit="submitting = true" class="space-y-6">
                @csrf

                <!-- STEP 1: Basic Details -->
                <div x-show="step === 1" x-transition.opacity>
                    <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-sky-600"></i> Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                            <input type="text" name="name" x-model="name" value="{{ old('name', $existingProfile->name ?? Auth::user()->name) }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username / Public Handle *</label>
                            <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                                <span class="bg-slate-100 text-slate-500 text-xs font-semibold px-3 flex items-center border-r border-slate-300">/p/</span>
                                <input type="text" name="username" x-model="username" value="{{ old('username', $existingProfile->slug ?? '') }}" required placeholder="john-doe" class="w-full px-3 py-3 text-sm outline-none"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Professional Details -->
                <div x-show="step === 2" x-transition.opacity style="display: none;">
                    <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-sky-600"></i> Professional Profile
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
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
                </div>

                <!-- STEP 3: Contact Information -->
                <div x-show="step === 3" x-transition.opacity style="display: none;">
                    <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-address-book text-sky-600"></i> Contact Information (for vCard Export)
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone</label>
                            <input type="text" name="phone" x-model="phone" @input="phone = phone.replace(/[^0-9]/g, '').slice(0, 10)" maxlength="10" pattern="[0-9]{10}" placeholder="9876543210" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition" :class="{'border-rose-400 focus:ring-rose-500': phone && phone.length !== 10}"/>
                            <p x-show="phone && phone.length !== 10" class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> Mobile number must be strictly 10 digits (0-9).
                            </p>
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

                <!-- STEP 4: Template Choice & Submit -->
                <div x-show="step === 4" x-transition.opacity style="display: none;">
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

                <!-- Back / Next Navigation Controls Bar -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button type="button" 
                            @click="prevStep()" 
                            :disabled="step === 1 || submitting" 
                            class="px-6 py-3 font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 disabled:opacity-40 rounded-xl transition text-sm flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </button>

                    <template x-if="step < totalSteps">
                        <button type="button" 
                                @click="nextStep()" 
                                :disabled="!canGoNext() || submitting" 
                                class="px-6 py-3 font-bold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-40 rounded-xl shadow-md transition text-sm flex items-center gap-2">
                            Next <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </template>

                    <template x-if="step === totalSteps">
                        <button type="submit" 
                                :disabled="submitting" 
                                class="px-6 py-3 font-black text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 rounded-xl shadow-lg transition text-sm flex items-center gap-2">
                            <span x-show="!submitting"><i class="fa-solid fa-qrcode mr-1"></i> Complete & Generate QR</span>
                            <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Processing...</span>
                        </button>
                    </template>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
