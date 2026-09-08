@extends('layouts.admin')

@section('title', 'System Audit Logs')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-black text-white">System Audit Logs</h2>
        <p class="text-xs text-slate-400 mt-1">Complete immutable audit trail of administrative activities and security events.</p>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, description, or IP..." class="bg-slate-900 text-white text-xs px-4 py-2.5 rounded-xl border border-slate-700 w-64 outline-none"/>
            <button type="submit" class="px-4 py-2.5 bg-purple-600 text-white font-bold text-xs rounded-xl hover:bg-purple-700">Filter</button>
        </form>

        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase border-b border-slate-700 pb-3">
                            <th class="pb-3">Timestamp</th>
                            <th>Admin / User</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-slate-300 font-semibold">
                        @foreach($logs as $l)
                            <tr>
                                <td class="py-3 font-mono text-[11px] text-slate-400">{{ $l->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="text-white">{{ $l->user ? $l->user->name : 'System' }}</td>
                                <td><span class="px-2 py-0.5 rounded bg-purple-950 text-purple-300 font-mono text-[11px]">{{ $l->action }}</span></td>
                                <td>{{ $l->target_type ? $l->target_type . ' #' . $l->target_id : 'N/A' }}</td>
                                <td>{{ $l->description }}</td>
                                <td class="font-mono text-[11px] text-slate-400">{{ $l->ip_address ?? '127.0.0.1' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @else
            <p class="text-xs text-slate-400 py-6 text-center">No audit logs recorded yet.</p>
        @endif
    </div>
</div>
@endsection
