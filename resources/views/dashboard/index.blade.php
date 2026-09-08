@extends('layouts.dashboard')

@section('title', 'Overview')

@section('content')
<!-- Overview Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">QR Profiles</span>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-id-card"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-slate-900">{{ number_format($totalProfiles) }}</p>
        <span class="text-xs font-semibold text-emerald-600 mt-2 block"><i class="fa-solid fa-check"></i> {{ $activeProfiles }} Active</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Scans</span>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-qrcode"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-slate-900">{{ number_format($totalScans) }}</p>
        <span class="text-xs font-medium text-slate-500 mt-2 block">Lifetime Scans</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Unique Visitors</span>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-slate-900">{{ number_format($uniqueVisitors) }}</p>
        <span class="text-xs font-medium text-slate-500 mt-2 block">Hashed IPs</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Link Clicks</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-arrow-pointer"></i>
            </div>
        </div>
        <p class="text-3xl font-black text-slate-900">{{ number_format($totalLinkClicks) }}</p>
        <span class="text-xs font-medium text-slate-500 mt-2 block">Outbound Clicks</span>
    </div>
</div>

<!-- Chart Section -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <h3 class="text-base font-bold text-slate-800 mb-4">Scans Over Time (Last 30 Days)</h3>
    <div class="h-64">
        <canvas id="scansChart"></canvas>
    </div>
</div>

<!-- Recent Profiles -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-800">Your Recent QR Profiles</h3>
        <a href="{{ route('dashboard.profiles.index') }}" class="text-sm font-bold text-sky-600 hover:underline">View All</a>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse($recentProfiles as $profile)
            <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">{{ $profile->name }}</h4>
                        <p class="text-xs text-slate-500 font-mono">/p/{{ $profile->slug }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $profile->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($profile->status) }}
                    </span>
                    <span class="text-xs font-bold text-slate-600"><i class="fa-solid fa-bolt text-amber-500 mr-1"></i> {{ $profile->scans_count }} Scans</span>
                    
                    <a href="{{ route('dashboard.profiles.edit', $profile->id) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg text-xs font-bold transition">Edit</a>
                    <a href="{{ route('dashboard.profiles.qr', $profile->id) }}" class="px-3 py-1.5 bg-sky-600 text-white hover:bg-sky-700 rounded-lg text-xs font-bold transition">QR Studio</a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-500">
                <p class="text-sm font-medium">No QR profiles created yet.</p>
                <a href="{{ route('dashboard.profiles.create') }}" class="mt-4 inline-block px-4 py-2 bg-sky-600 text-white rounded-xl font-bold text-sm">Create First Profile</a>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rawData = @json($scansOverTime);
        const labels = Object.keys(rawData);
        const data = Object.values(rawData);

        const ctx = document.getElementById('scansChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [{
                    label: 'Scans',
                    data: data.length ? data : [0],
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endsection
