@extends('layouts.admin')

@section('title', 'Custom Domains Moderation')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-white">Custom Domains</h2>
            <p class="text-xs text-slate-400 mt-1">Inspect, verify, and moderate custom user domain mappings.</p>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by domain or user..." class="bg-slate-900 text-white text-xs px-4 py-2.5 rounded-xl border border-slate-700 w-64 outline-none"/>
            <select name="status" class="bg-slate-900 text-white text-xs px-4 py-2.5 rounded-xl border border-slate-700 outline-none">
                <option value="">All Statuses</option>
                <option value="pending_dns" {{ request('status') === 'pending_dns' ? 'selected' : '' }}>Pending DNS</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-purple-600 text-white font-bold text-xs rounded-xl hover:bg-purple-700">Filter</button>
        </form>

        @if($domains->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase border-b border-slate-700 pb-3">
                            <th class="pb-3">Domain</th>
                            <th>Owner</th>
                            <th>TXT Token</th>
                            <th>Status</th>
                            <th>SSL</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-slate-300 font-semibold">
                        @foreach($domains as $d)
                            <tr>
                                <td class="py-4 font-bold text-white">{{ $d->domain }}</td>
                                <td>{{ $d->user ? $d->user->name : 'N/A' }} ({{ $d->user ? $d->user->email : '' }})</td>
                                <td class="font-mono text-[11px] text-purple-400">{{ $d->verification_token }}</td>
                                <td>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $d->status === 'active' ? 'bg-emerald-900/60 text-emerald-400' : 'bg-amber-900/60 text-amber-400' }}">
                                        {{ $d->status }}
                                    </span>
                                </td>
                                <td><span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 text-[11px]">{{ $d->ssl_status ?? 'pending' }}</span></td>
                                <td class="text-right flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.domains.verify', $d->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded text-[11px] font-bold">Verify DNS</button>
                                    </form>
                                    <form action="{{ route('admin.domains.toggle', $d->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-slate-700 text-slate-200 rounded text-[11px] font-bold">Toggle</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $domains->links() }}
            </div>
        @else
            <p class="text-xs text-slate-400 py-6 text-center">No custom domains found.</p>
        @endif
    </div>
</div>
@endsection
