@extends('layouts.admin')

@section('title', 'Profile Reports Moderation')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Profile Abuse Reports</h1>
            <p class="text-xs text-slate-500 mt-1">Review user-submitted reports for malicious, spam, or abusive profile content.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.profile-reports.index') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">All</a>
            <a href="{{ route('admin.profile-reports.index', ['status' => 'pending']) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Pending</a>
            <a href="{{ route('admin.profile-reports.index', ['status' => 'reviewed']) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ request('status') === 'reviewed' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Reviewed</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Report ID</th>
                        <th class="p-4">Reported Profile</th>
                        <th class="p-4">Reason</th>
                        <th class="p-4">Description</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-4 font-mono text-slate-500">#{{ $report->id }}</td>
                            <td class="p-4">
                                @if($report->profile)
                                    <div>
                                        <a href="{{ route('profile.show', $report->profile->slug) }}" target="_blank" class="font-bold text-sky-600 hover:underline">
                                            {{ $report->profile->name }}
                                        </a>
                                        <span class="block text-[10px] text-slate-400 font-mono">/p/{{ $report->profile->slug }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Deleted Profile</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800">
                                    {{ $report->reason }}
                                </span>
                            </td>
                            <td class="p-4 max-w-xs truncate text-slate-500">
                                {{ $report->description ?: 'No detailed description provided.' }}
                            </td>
                            <td class="p-4">
                                @if($report->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                @elseif($report->status === 'reviewed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Reviewed</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600 border border-slate-200">Dismissed</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-400 font-mono text-[11px]">
                                {{ $report->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($report->status === 'pending')
                                        <form action="{{ route('admin.profile-reports.update-status', $report->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="dismissed">
                                            <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">Dismiss</button>
                                        </form>
                                    @endif

                                    @if($report->profile && $report->profile->status !== 'suspended')
                                        <form action="{{ route('admin.profile-reports.suspend', $report->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to suspend this profile?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow transition">Suspend Profile</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">No profile reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
