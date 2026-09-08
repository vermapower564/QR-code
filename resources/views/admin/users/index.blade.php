@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-white">Users</h2>

    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500 w-64"/>
        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold">Search</button>
    </form>
</div>

<div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden">
    <table class="w-full text-left text-xs">
        <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-800">
            <tr>
                <th class="p-4">User</th>
                <th class="p-4">Email</th>
                <th class="p-4">Plan</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800 text-slate-300">
            @foreach($users as $user)
                <tr>
                    <td class="p-4 font-bold text-white">{{ $user->name }}</td>
                    <td class="p-4 font-mono">{{ $user->email }}</td>
                    <td class="p-4 font-semibold text-purple-400">{{ $user->plan ? $user->plan->name : 'Free' }}</td>
                    <td class="p-4">
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $user->status === 'active' ? 'bg-emerald-950 text-emerald-300' : 'bg-rose-950 text-rose-300' }}">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded text-[11px] font-bold">
                                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
