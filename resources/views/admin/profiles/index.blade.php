@extends('layouts.admin')

@section('title', 'QR Profile Moderation')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-white">All QR Profiles</h2>

    <form action="{{ route('admin.profiles.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or slug..." class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none w-64"/>
        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold">Search</button>
    </form>
</div>

<div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden">
    <table class="w-full text-left text-xs">
        <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-800">
            <tr>
                <th class="p-4">Profile Name</th>
                <th class="p-4">Owner</th>
                <th class="p-4">Slug</th>
                <th class="p-4">Scans</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800 text-slate-300">
            @foreach($profiles as $p)
                <tr>
                    <td class="p-4 font-bold text-white">{{ $p->name }}</td>
                    <td class="p-4">{{ $p->user ? $p->user->name : 'N/A' }}</td>
                    <td class="p-4 font-mono text-purple-400">/p/{{ $p->slug }}</td>
                    <td class="p-4 font-bold">{{ $p->scans_count }}</td>
                    <td class="p-4">
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $p->status === 'active' ? 'bg-emerald-950 text-emerald-300' : 'bg-rose-950 text-rose-300' }}">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <a href="{{ route('profile.show', $p->slug) }}" target="_blank" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded text-[11px] font-bold">View</a>
                        <form action="{{ route('admin.profiles.toggle', $p->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-rose-900 hover:bg-rose-800 text-rose-200 rounded text-[11px] font-bold">
                                {{ $p->status === 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $profiles->links() }}
</div>
@endsection
