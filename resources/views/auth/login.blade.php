@extends('layouts.public')

@section('title', 'Log In - QR Identity')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-xl flex items-center justify-center mx-auto shadow-md mb-3">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Welcome Back</h2>
            <p class="text-xs text-slate-500 mt-1">Sign in to manage your QR profiles & analytics</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-xs p-3.5 rounded-xl font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
            </div>

            <div class="flex items-center justify-between text-xs font-semibold">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-sky-600 focus:ring-sky-500"/>
                    <span class="text-slate-600">Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sky-600 hover:underline">Forgot Password?</a>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20 transition text-sm">
                Log In
            </button>
        </form>

        <p class="text-center text-xs text-slate-600 mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="font-bold text-sky-600 hover:underline">Create Account</a>
        </p>
    </div>
</div>
@endsection
