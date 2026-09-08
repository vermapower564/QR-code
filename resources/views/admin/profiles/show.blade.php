@extends('layouts.admin')

@section('title', 'Profile Details - ' . $profile->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.profiles.index') }}" class="text-xs font-bold text-slate-400 hover:text-white mb-2 inline-block">&larr; Back to Profiles List</a>
            <h2 class="text-2xl font-black text-white flex items-center gap-3">
                <span>{{ $profile->name }}</span>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold uppercase {{ $profile->status === 'active' ? 'bg-emerald-900/60 text-emerald-400 border border-emerald-700' : 'bg-rose-900/60 text-rose-400 border border-rose-700' }}">
                    {{ $profile->status ?? 'active' }}
                </span>
            </h2>
            <p class="text-xs text-slate-400 font-mono mt-1">/p/{{ $profile->slug }} • Owned by {{ $profile->user ? $profile->user->name : 'Unknown' }} ({{ $profile->user ? $profile->user->email : '' }})</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/p/' . $profile->slug) }}" target="_blank" class="px-4 py-2 text-xs font-bold rounded-xl bg-purple-600 text-white hover:bg-purple-700 shadow">
                Preview Public Profile <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i>
            </a>
            <form action="{{ route('admin.profiles.toggle', $profile->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl border transition {{ $profile->status === 'active' ? 'border-rose-700 text-rose-400 hover:bg-rose-950' : 'border-emerald-700 text-emerald-400 hover:bg-emerald-950' }}">
                    {{ $profile->status === 'active' ? 'Suspend Profile' : 'Activate Profile' }}
                </button>
            </form>
            <form action="{{ route('admin.profiles.destroy', $profile->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this profile?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-rose-600 text-white hover:bg-rose-700 shadow">
                    Delete Profile
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase">Total QR Scans</span>
            <p class="text-3xl font-black text-white mt-1">{{ $profile->scans_count }}</p>
            <span class="text-[11px] text-slate-400 mt-1 block">Dynamic QR scan interactions</span>
        </div>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase">Social Links</span>
            <p class="text-3xl font-black text-white mt-1">{{ $profile->socialLinks->count() }}</p>
            <span class="text-[11px] text-slate-400 mt-1 block">Connected social platforms</span>
        </div>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase">Custom Links</span>
            <p class="text-3xl font-black text-white mt-1">{{ $profile->customLinks->count() }}</p>
            <span class="text-[11px] text-slate-400 mt-1 block">Custom call-to-action buttons</span>
        </div>
    </div>
</div>
@endsection
