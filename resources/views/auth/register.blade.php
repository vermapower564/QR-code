@extends('layouts.public')

@section('title', 'Create Account - QR Identity')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200" 
         x-data="{ 
            submitting: false, 
            showPassword: false, 
            showConfirmPassword: false,
            password: '',
            get strength() {
                if (!this.password) return { label: '', color: '', percent: 0 };
                let score = 0;
                if (this.password.length >= 8) score++;
                if (/[A-Z]/.test(this.password)) score++;
                if (/[a-z]/.test(this.password)) score++;
                if (/[0-9]/.test(this.password)) score++;
                if (/[^A-Za-z0-9]/.test(this.password)) score++;

                if (score <= 2) return { label: 'Weak', color: 'bg-rose-500 text-rose-700', percent: 33 };
                if (score <= 4) return { label: 'Medium', color: 'bg-amber-500 text-amber-700', percent: 66 };
                return { label: 'Strong', color: 'bg-emerald-500 text-emerald-700', percent: 100 };
            }
         }">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-xl flex items-center justify-center mx-auto shadow-md mb-3">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Create your account</h2>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Create your digital profile and generate your dynamic QR code.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-xs p-4 rounded-xl">
                <ul class="list-disc pl-4 space-y-1 font-semibold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" @submit="submitting = true" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Website / URL Link</label>
                <input type="url" name="website" value="{{ old('website') }}" placeholder="https://example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                <span class="text-[11px] text-slate-400 mt-1 block">Destination link associated with your profile (e.g. https://example.com)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Company (Optional)</label>
                    <input type="text" name="company" value="{{ old('company') }}" placeholder="ABC Corp" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone (Optional)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="9876543210" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required placeholder="Minimum 8 chars (e.g. Roushan@123)" class="w-full px-4 py-3 pr-10 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm p-1 focus:outline-none" aria-label="Toggle Password Visibility">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div x-show="password.length > 0" class="mt-2 space-y-1" x-transition>
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500">Password Strength:</span>
                        <span :class="strength.color" x-text="strength.label" class="px-1.5 py-0.5 rounded"></span>
                    </div>
                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-300" :class="strength.color" :style="`width: ${strength.percent}%`"></div>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Requires 8+ chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol (e.g. <span class="font-mono text-slate-600">Roushan@123</span>)</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm Password *</label>
                <div class="relative">
                    <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required placeholder="Re-enter password" class="w-full px-4 py-3 pr-10 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm p-1 focus:outline-none" aria-label="Toggle Confirm Password Visibility">
                        <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit" :disabled="submitting" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 rounded-xl shadow-md shadow-sky-500/20 transition text-sm flex items-center justify-center gap-2">
                <span x-show="!submitting">Create Account</span>
                <span x-show="submitting"><i class="fa-solid fa-circle-notch fa-spin"></i> Creating Account...</span>
            </button>
        </form>

        <p class="text-center text-xs text-slate-600 mt-6">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-sky-600 hover:underline">Log in</a>
        </p>
    </div>
</div>
@endsection
