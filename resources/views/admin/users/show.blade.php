@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-400 hover:text-white mb-2 inline-block">&larr; Back to Users List</a>
            <h2 class="text-2xl font-black text-white flex items-center gap-3">
                <span>{{ $user->name }}</span>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold uppercase {{ $user->status === 'active' ? 'bg-emerald-900/60 text-emerald-400 border border-emerald-700' : 'bg-rose-900/60 text-rose-400 border border-rose-700' }}">
                    {{ $user->status ?? 'active' }}
                </span>
            </h2>
            <p class="text-xs text-slate-400 font-mono mt-1">{{ $user->email }} • Joined {{ $user->created_at->format('M d, Y') }}</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl border transition {{ $user->status === 'active' ? 'border-rose-700 text-rose-400 hover:bg-rose-950' : 'border-emerald-700 text-emerald-400 hover:bg-emerald-950' }}">
                    {{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                </button>
            </form>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-rose-600 text-white hover:bg-rose-700 shadow">
                    Delete User
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Overview Stats -->
        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase">Assigned Plan</span>
            <h3 class="text-xl font-bold text-white mt-1">{{ $user->plan ? $user->plan->name : 'Free Tier' }}</h3>
            <form action="{{ route('admin.users.plan', $user->id) }}" method="POST" class="mt-4 flex gap-2">
                @csrf
                <select name="plan_id" class="bg-slate-900 text-white text-xs p-2 rounded-xl border border-slate-700 w-full outline-none">
                    @foreach(\App\Models\Plan::all() as $plan)
                        <option value="{{ $plan->id }}" {{ $user->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }} (${{ $plan->price }}/mo)</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-purple-600 text-white font-bold text-xs rounded-xl hover:bg-purple-700">Update</button>
            </form>
        </div>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Profiles</span>
            <p class="text-3xl font-black text-white mt-1">{{ $user->profiles->count() }}</p>
            <span class="text-[11px] text-slate-400 mt-1 block">Active digital QR profiles</span>
        </div>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Payments</span>
            <p class="text-3xl font-black text-white mt-1">${{ number_format($user->payments->where('status', 'completed')->sum('amount'), 2) }}</p>
            <span class="text-[11px] text-slate-400 mt-1 block">Completed billing payments</span>
        </div>
    </div>

    <!-- User's QR Profiles -->
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
        <h3 class="text-base font-bold text-white mb-4">User's QR Profiles</h3>
        @if($user->profiles->count() > 0)
            <table class="w-full text-left text-xs">
                <thead><tr class="text-slate-400 border-b border-slate-700 pb-2"><th>Profile Name</th><th>Slug</th><th>Status</th><th>Action</th></tr></thead>
                <tbody class="divide-y divide-slate-700 text-slate-300 font-semibold">
                    @foreach($user->profiles as $p)
                        <tr>
                            <td class="py-3 font-bold text-white">{{ $p->name }}</td>
                            <td class="font-mono text-purple-400">/p/{{ $p->slug }}</td>
                            <td><span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 text-[11px]">{{ $p->status }}</span></td>
                            <td><a href="{{ route('admin.profiles.show', $p->id) }}" class="text-purple-400 hover:underline">Manage Profile</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-xs text-slate-400">No QR profiles created by this user yet.</p>
        @endif
    </div>
</div>
@endsection
