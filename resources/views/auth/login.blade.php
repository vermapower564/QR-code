@extends('layouts.public')

@section('title', 'Sign In - QR Identity')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-200">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-900">Welcome Back</h2>
            <p class="text-sm text-slate-500 mt-1">Sign in to manage your QR profiles</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-sm p-3.5 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition text-sm"/>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition text-sm"/>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded text-sky-600 focus:ring-sky-500"/>
                    <span class="text-slate-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20 transition text-sm">
                Sign In
            </button>
        </form>

        <p class="text-center text-sm text-slate-600 mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="font-bold text-sky-600 hover:underline">Register free</a>
        </p>
    </div>
</div>
@endsection
