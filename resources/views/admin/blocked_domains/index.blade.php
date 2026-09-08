@extends('layouts.admin')

@section('title', 'Domain Blocklist Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Blocked Domains & Anti-Abuse</h1>
            <p class="text-xs text-slate-500 mt-1">Manage global domain blocklist to prevent phishing, malware, or spam URLs on profile links.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-slate-900">Add New Blocked Domain</h3>
        <form action="{{ route('admin.blocked-domains.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            @csrf
            <div class="sm:col-span-5">
                <input type="text" name="domain" placeholder="e.g. malicious-site.com" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none"/>
            </div>
            <div class="sm:col-span-5">
                <input type="text" name="reason" placeholder="Reason (e.g. Phishing / Malware destination)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none"/>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow transition">Block Domain</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Domain</th>
                        <th class="p-4">Reason</th>
                        <th class="p-4">Added By</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                    @forelse($domains as $domain)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-4 font-mono font-bold text-slate-900">{{ $domain->domain }}</td>
                            <td class="p-4 text-slate-600">{{ $domain->reason }}</td>
                            <td class="p-4 text-slate-500">{{ $domain->creator?->name ?? 'System' }}</td>
                            <td class="p-4 text-slate-400 font-mono text-[11px]">{{ $domain->created_at->format('M d, Y') }}</td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.blocked-domains.destroy', $domain->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove domain from blocklist?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 font-bold rounded-xl text-xs transition">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">No blocked domains added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($domains->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $domains->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
