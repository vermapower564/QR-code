@extends('layouts.dashboard')

@section('title', 'Analytics - ' . $profile->name)

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-xl font-bold text-slate-900">Analytics Report: {{ $profile->name }}</h2>
        <p class="text-xs text-slate-500 font-mono mt-0.5">/p/{{ $profile->slug }}</p>
    </div>
    <a href="{{ route('dashboard.profiles.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Profiles
    </a>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Total QR Scans</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['total_scans']) }}</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Unique Visitors</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['unique_visitors']) }}</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Outbound Clicks</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['total_clicks']) }}</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Contacts Saved (.VCF)</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['save_contact_count']) }}</p>
    </div>
</div>

<!-- Daily Scans Chart -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <h3 class="text-base font-bold text-slate-800 mb-4">Daily Scan Traffic (Last 30 Days)</h3>
    <div class="h-64">
        <canvas id="dailyScansChart"></canvas>
    </div>
</div>

<!-- Device & Browser Distribution -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-bold text-slate-800 mb-4">Device Distribution</h3>
        <div class="h-48">
            <canvas id="deviceChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-bold text-slate-800 mb-4">Browser Breakdown</h3>
        <div class="h-48">
            <canvas id="browserChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daily Scans
        const dailyData = @json($stats['daily_scans']);
        new Chart(document.getElementById('dailyScansChart'), {
            type: 'line',
            data: {
                labels: Object.keys(dailyData).length ? Object.keys(dailyData) : ['No Data'],
                datasets: [{
                    label: 'Scans',
                    data: Object.values(dailyData).length ? Object.values(dailyData) : [0],
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Device Pie
        const deviceData = @json($stats['devices']);
        new Chart(document.getElementById('deviceChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(deviceData).length ? Object.keys(deviceData) : ['None'],
                datasets: [{
                    data: Object.values(deviceData).length ? Object.values(deviceData) : [1],
                    backgroundColor: ['#0284c7', '#6366f1', '#a855f7', '#ec4899']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Browser Pie
        const browserData = @json($stats['browsers']);
        new Chart(document.getElementById('browserChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(browserData).length ? Object.keys(browserData) : ['None'],
                datasets: [{
                    label: 'Scans',
                    data: Object.values(browserData).length ? Object.values(browserData) : [0],
                    backgroundColor: '#0284c7'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    });
</script>
@endsection
