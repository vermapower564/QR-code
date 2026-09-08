@extends('layouts.dashboard')

@section('title', 'My QR Profiles')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900">QR Profiles</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage your dynamic profile cards and QR codes</p>
    </div>
    <a href="{{ route('dashboard.profiles.create') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-bold shadow-sm transition">
        <i class="fa-solid fa-plus text-xs mr-1.5"></i> Create Profile
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($profiles as $profile)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <!-- Top Status Badge & Actions -->
                <div class="flex items-center justify-between mb-4">
                    <form action="{{ route('dashboard.profiles.toggle', $profile->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition {{ $profile->status === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $profile->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ ucfirst($profile->status) }}
                        </button>
                    </form>
                    
                    <span class="text-xs font-bold text-slate-500">
                        <i class="fa-solid fa-qrcode text-sky-600 mr-1"></i> {{ number_format($profile->scans_count) }} Scans
                    </span>
                </div>

                <!-- Profile Info -->
                <div class="flex items-center gap-4 mb-4">
                    @if($profile->profile_image)
                        <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="{{ $profile->name }}" class="w-14 h-14 rounded-full object-cover border border-slate-200"/>
                    @else
                        <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-xl flex items-center justify-center">
                            {{ strtoupper(substr($profile->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-900 text-base truncate">{{ $profile->name }}</h3>
                        <p class="text-xs text-slate-500 truncate">{{ $profile->designation ?: 'Personal Profile' }}</p>
                        <a href="{{ route('profile.show', $profile->slug) }}" target="_blank" class="text-xs font-mono text-sky-600 hover:underline block truncate mt-0.5">
                            /p/{{ $profile->slug }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-0.5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Grid -->
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('dashboard.profiles.edit', $profile->id) }}" class="py-2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        <i class="fa-solid fa-pen text-slate-500 mr-1"></i> Edit
                    </a>
                    <a href="{{ route('dashboard.profiles.analytics', $profile->id) }}" class="py-2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        <i class="fa-solid fa-chart-simple text-purple-500 mr-1"></i> Stats
                    </a>
                    <a href="{{ route('dashboard.profiles.qr', $profile->id) }}" class="py-2 text-center bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                        <i class="fa-solid fa-qrcode mr-1"></i> QR
                    </a>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <form action="{{ route('dashboard.profiles.duplicate', $profile->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-slate-500 hover:text-slate-900 font-semibold">
                            <i class="fa-solid fa-copy mr-1"></i> Duplicate
                        </button>
                    </form>

                    <form action="{{ route('dashboard.profiles.destroy', $profile->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this profile?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold">
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-12 rounded-2xl border border-slate-200 text-center">
            <div class="w-16 h-16 bg-sky-50 text-sky-600 rounded-full flex items-center justify-center font-bold text-2xl mx-auto mb-4">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No QR Profiles Yet</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Create your first dynamic profile card to generate a unique QR code.</p>
            <a href="{{ route('dashboard.profiles.create') }}" class="mt-6 inline-block px-6 py-3 bg-sky-600 text-white font-bold text-sm rounded-xl shadow-md">Create Profile</a>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $profiles->links() }}
</div>
@endsection
