@extends('layouts.public')

@section('title', 'Forgot Password - QR Identity')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 font-black text-xl flex items-center justify-center mx-auto shadow-sm mb-3">
                <i class="fa-solid fa-key"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Forgot Password</h2>
            <p class="text-xs text-slate-500 mt-1">Enter your email and we'll send you a password reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-3.5 rounded-xl font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition"/>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md transition text-sm">
                Send Password Reset Link
            </button>
        </form>

        <p class="text-center text-xs text-slate-600 mt-6">
            Remembered your password? <a href="{{ route('login') }}" class="font-bold text-sky-600 hover:underline">Log in</a>
        </p>
    </div>
</div>
@endsection
