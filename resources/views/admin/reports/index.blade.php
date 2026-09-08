@extends('layouts.admin')

@section('title', 'System Reports & CSV Export')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-white">System Reports & Data Export</h2>
            <p class="text-xs text-slate-400 mt-1">Download CSV reports for users, profiles, and scan analytics.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.export', ['type' => 'users']) }}" class="px-4 py-2 bg-purple-600 text-white font-bold text-xs rounded-xl shadow hover:bg-purple-700">
                <i class="fa-solid fa-download mr-1.5"></i> Export Users CSV
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'profiles']) }}" class="px-4 py-2 bg-sky-600 text-white font-bold text-xs rounded-xl shadow hover:bg-sky-700">
                <i class="fa-solid fa-download mr-1.5"></i> Export Profiles CSV
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'scans']) }}" class="px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow hover:bg-emerald-700">
                <i class="fa-solid fa-download mr-1.5"></i> Export Scans CSV
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <h3 class="text-sm font-bold text-white mb-3">Top Browsers</h3>
            <ul class="space-y-2 text-xs text-slate-300">
                @foreach($scanStatsByBrowser as $b)
                    <li class="flex justify-between font-semibold"><span>{{ $b->browser ?? 'Unknown' }}</span><span class="bg-slate-900 px-2 py-0.5 rounded text-purple-400 font-mono">{{ $b->count }}</span></li>
                @endforeach
            </ul>
        </div>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <h3 class="text-sm font-bold text-white mb-3">Top Devices</h3>
            <ul class="space-y-2 text-xs text-slate-300">
                @foreach($scanStatsByDevice as $d)
                    <li class="flex justify-between font-semibold"><span>{{ $d->device_type ?? 'Mobile' }}</span><span class="bg-slate-900 px-2 py-0.5 rounded text-sky-400 font-mono">{{ $d->count }}</span></li>
                @endforeach
            </ul>
        </div>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <h3 class="text-sm font-bold text-white mb-3">Top Countries</h3>
            <ul class="space-y-2 text-xs text-slate-300">
                @foreach($scanStatsByCountry as $c)
                    <li class="flex justify-between font-semibold"><span>{{ $c->country ?? 'United States' }}</span><span class="bg-slate-900 px-2 py-0.5 rounded text-emerald-400 font-mono">{{ $c->count }}</span></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
