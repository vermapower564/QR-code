@extends('layouts.dashboard')

@section('title', 'Create QR Profile')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
    <div class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-xl font-bold text-slate-900">Create New Profile</h2>
        <p class="text-xs text-slate-500 mt-0.5">Fill in your profile details to generate your dynamic QR code.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-sm p-4 rounded-xl">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.profiles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username / Slug *</label>
                <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                    <span class="bg-slate-100 px-3 py-3 text-slate-500 text-xs font-mono border-r border-slate-300 flex items-center">/p/</span>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="john-doe" required class="w-full px-3 py-3 text-sm outline-none border-none"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Designation / Title</label>
                <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. CEO & Co-founder" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company / Organization</label>
                <input type="text" name="company" value="{{ old('company') }}" placeholder="e.g. ABC Technologies" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 234 567 8900" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none"/>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Website URL</label>
            <input type="url" name="website" value="{{ old('website') }}" placeholder="https://example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none"/>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bio / Description</label>
            <textarea name="bio" rows="3" placeholder="Technology entrepreneur, speaker, software enthusiast..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm outline-none">{{ old('bio') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Profile Avatar Image</label>
                <input type="file" name="profile_image" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company Logo</label>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"/>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('dashboard.profiles.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-bold shadow-md transition">Create & Next</button>
        </div>
    </form>
</div>
@endsection
