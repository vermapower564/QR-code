@extends('layouts.public')

@section('title', 'Create Account - QR Identity')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200">
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

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none transition"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none transition"/>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Company (Optional)</label>
                    <input type="text" name="company" value="{{ old('company') }}" placeholder="ABC Corp" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none transition"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone (Optional)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+123456789" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none transition"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                <input type="password" name="password" required placeholder="Minimum 8 characters" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none transition"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm Password *</label>
                <input type="password" name="password_confirmation" required placeholder="Re-enter password" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none transition"/>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20 transition text-sm">
                Create Account
            </button>
        </form>

        <p class="text-center text-xs text-slate-600 mt-6">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-sky-600 hover:underline">Log in</a>
        </p>
    </div>
</div>
@endsection
