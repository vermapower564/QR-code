@extends('layouts.dashboard')

@section('title', 'Account Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4">Personal Information</h3>

        <form action="{{ route('dashboard.settings.profile') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-sm transition">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4">Update Password</h3>

        <form action="{{ route('dashboard.settings.password') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Current Password</label>
                <input type="password" name="current_password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">New Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm transition">Change Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
