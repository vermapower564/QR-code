@extends('layouts.public')

@section('title', 'Verify Email Address - QR Identity')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200 text-center">
        <div class="w-16 h-16 rounded-3xl bg-sky-50 text-sky-600 font-black text-2xl flex items-center justify-center mx-auto shadow-sm mb-4">
            <i class="fa-solid fa-envelope-open-text"></i>
        </div>

        <h2 class="text-2xl font-black text-slate-900">Verify your email address</h2>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed max-w-sm mx-auto">
            Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mt-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-3.5 rounded-xl font-semibold">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="mt-8 space-y-3">
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20 transition text-sm">
                    Resend Verification Email
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition py-2">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
