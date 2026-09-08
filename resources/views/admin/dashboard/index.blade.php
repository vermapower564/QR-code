@extends('layouts.admin')

@section('title', 'Admin Console - Overview')

@section('content')
<!-- Overview Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Total Users</span>
        <p class="text-3xl font-black text-white">{{ number_format($totalUsers) }}</p>
    </div>

    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Total Profiles</span>
        <p class="text-3xl font-black text-white">{{ number_format($totalProfiles) }}</p>
    </div>

    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Total QR Scans</span>
        <p class="text-3xl font-black text-white">{{ number_format($totalScans) }}</p>
    </div>

    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Total Revenue</span>
        <p class="text-3xl font-black text-emerald-400">${{ number_format($totalRevenue, 2) }}</p>
    </div>
</div>

<!-- Recent Registered Users -->
<div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden mb-8">
    <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <h3 class="font-bold text-white text-base">Recent Registered Users</h3>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-purple-400 hover:underline">View All Users</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-800">
                <tr>
                    <th class="p-4">Name</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Role</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                @foreach($recentUsers as $user)
                    <tr>
                        <td class="p-4 font-bold text-white">{{ $user->name }}</td>
                        <td class="p-4 font-mono">{{ $user->email }}</td>
                        <td class="p-4"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $user->role === 'admin' ? 'bg-purple-900 text-purple-300' : 'bg-slate-800 text-slate-300' }}">{{ $user->role }}</span></td>
                        <td class="p-4"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $user->status === 'active' ? 'bg-emerald-950 text-emerald-300' : 'bg-rose-950 text-rose-300' }}">{{ $user->status }}</span></td>
                        <td class="p-4 text-slate-400">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
