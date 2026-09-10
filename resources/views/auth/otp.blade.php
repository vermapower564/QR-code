@extends('layouts.public')

@section('title', 'Verify OTP - QR Identity')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 font-black text-xl flex items-center justify-center mx-auto shadow-sm mb-3">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Enter OTP</h2>
            <p class="text-xs text-slate-500 mt-1">We sent a 6-digit code to your email.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 text-xs p-3.5 rounded-xl font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.otp.verify') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">6-Digit Code</label>
                <input type="text" name="otp" required maxlength="6" pattern="\d{6}" placeholder="123456" class="w-full px-4 py-3 text-center tracking-[0.5em] font-mono rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-lg outline-none transition"/>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md transition text-sm">
                Verify OTP
            </button>
        </form>

        <p class="text-center text-xs text-slate-600 mt-6">
            <a href="{{ route('password.request') }}" class="font-bold text-sky-600 hover:underline">Resend OTP</a>
        </p>
    </div>
</div>
@endsection
