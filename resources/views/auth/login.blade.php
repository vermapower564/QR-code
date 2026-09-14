@extends('layouts.public')

@section('title', 'Log In to Open User Dashboard - QR Identity')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200" 
         x-data="{ 
            submitting: false, 
            showPassword: false, 
            email: '{{ old('email') }}', 
            password: '',
            fillAccount(accEmail, accPass) {
                this.email = accEmail;
                this.password = accPass;
            }
         }">
        
        <!-- Header Icon & Titles -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-md mb-3">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <span class="inline-block px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-[10px] font-black uppercase tracking-wider mb-2">User Dashboard Access</span>
            <h2 class="text-2xl font-black text-sky-600">Sign In to Open Dashboard</h2>
            <p class="text-xs text-slate-500 mt-1">Please enter your Email ID and Password to unlock your user profile and dashboard.</p>
        </div>

        <!-- Warning Banner for unauthorized attempt -->
        @if(request('auth_required'))
            <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 text-xs p-4 rounded-2xl font-semibold flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-lock text-amber-600 text-base mt-0.5 shrink-0"></i>
                <div>
                    <span class="font-black block uppercase text-[10px] tracking-wider text-amber-700">Authentication Required</span>
                    <span>Without your Email ID and Password, the user page cannot be opened. Enter your credentials below to proceed.</span>
                </div>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-3.5 rounded-2xl font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-xs p-3.5 rounded-2xl font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Login Form Box -->
        <form action="{{ route('login') }}" method="POST" @submit="submitting = true" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    <i class="fa-regular fa-envelope text-sky-600 mr-1"></i> Email ID
                </label>
                <input type="email" name="email" x-model="email" required placeholder="name@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition font-medium"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-key text-sky-600 mr-1"></i> Password
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required placeholder="Enter password" class="w-full px-4 py-3 pr-10 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition font-medium"/>
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm p-1 focus:outline-none" aria-label="Toggle Password Visibility">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs font-semibold pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-sky-600 focus:ring-sky-500"/>
                    <span class="text-slate-600">Keep me logged in</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sky-600 hover:underline">Forgot Password?</a>
            </div>

            <button type="submit" :disabled="submitting" class="w-full py-3.5 px-4 font-black text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 rounded-xl shadow-lg shadow-sky-500/25 transition text-sm flex items-center justify-center gap-2">
                <span x-show="!submitting" class="flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Verify & Open Dashboard
                </span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...
                </span>
            </button>
        </form>

        <!-- 1-Click Quick Fill Demo Accounts -->
        <div class="mt-6 pt-6 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Quick-Fill Demo Accounts:</span>
                <span class="text-[10px] font-bold text-slate-400">Pass: password</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="fillAccount('john@example.com', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 text-left transition group">
                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-sky-600 truncate">John Doe (CEO)</div>
                    <div class="text-[10px] text-slate-400 font-mono">john@example.com</div>
                </button>
                <button type="button" @click="fillAccount('sarah@example.com', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 text-left transition group">
                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-sky-600 truncate">Sarah (Creator)</div>
                    <div class="text-[10px] text-slate-400 font-mono">sarah@example.com</div>
                </button>
                <button type="button" @click="fillAccount('alex@example.com', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 text-left transition group">
                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-sky-600 truncate">Alex (Dev)</div>
                    <div class="text-[10px] text-slate-400 font-mono">alex@example.com</div>
                </button>
                <button type="button" @click="fillAccount('admin@qrsocialsaas.com', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-200 text-left transition group">
                    <div class="text-[11px] font-bold text-indigo-700 truncate">System Admin</div>
                    <div class="text-[10px] text-slate-400 font-mono">admin@...</div>
                </button>
            </div>
        </div>

        <!-- Registration Link -->
        <p class="text-center text-xs text-slate-600 mt-6">
            Need a new account? <a href="{{ route('register') }}" class="font-bold text-sky-600 hover:underline">Create Free Account</a>
        </p>
    </div>
</div>
@endsection
