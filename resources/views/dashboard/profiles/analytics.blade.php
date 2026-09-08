@extends('layouts.dashboard')

@section('title', 'Analytics - ' . $profile->name)

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-xl font-bold text-slate-900">Analytics Dashboard: {{ $profile->name }}</h2>
        <p class="text-xs text-slate-500 font-mono mt-0.5">/p/{{ $profile->slug }} • Range: {{ $stats['start_date'] }} - {{ $stats['end_date'] }}</p>
    </div>
    
    <div class="flex items-center gap-3 flex-wrap">
        <!-- Date Range Selector Form -->
        <form method="GET" action="{{ route('dashboard.profiles.analytics', $profile->id) }}" class="flex items-center gap-2">
            <select name="range" onchange="this.form.submit()" class="text-xs font-semibold bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 shadow-sm focus:ring-2 focus:ring-sky-500">
                <option value="today" {{ ($range ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                <option value="7_days" {{ ($range ?? '') === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30_days" {{ ($range ?? '30_days') === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_month" {{ ($range ?? '') === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ ($range ?? '') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ ($range ?? '') === 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>

        <!-- CSV Export Button -->
        <a href="{{ route('dashboard.profiles.analytics', $profile->id) }}?export=true&range={{ $range }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow transition-colors">
            <i class="fa-solid fa-download"></i> Export CSV
        </a>

        <a href="{{ route('dashboard.profiles.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 ml-2">
            <i class="fa-solid fa-arrow-left mr-1"></i> Profiles
        </a>
    </div>
</div>

<!-- Primary KPI Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total QR Scans</span>
            <span class="text-xs font-bold {{ $stats['growth_percentage'] >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-2 py-0.5 rounded-full">
                {{ $stats['growth_percentage'] >= 0 ? '↑ +' : '↓ ' }}{{ $stats['growth_percentage'] }}%
            </span>
        </div>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['total_scans']) }}</p>
        <span class="text-[11px] text-slate-400 mt-1 block">vs previous period</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Unique Visitors</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['unique_visitors']) }}</p>
        <span class="text-[11px] text-slate-400 mt-1 block">Hashed 24h visitors</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Outbound Clicks</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['total_clicks']) }}</p>
        <span class="text-[11px] text-slate-400 mt-1 block">Social & custom links</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Contacts Saved (.VCF)</span>
        <p class="text-3xl font-black text-slate-900">{{ number_format($stats['save_contact_count']) }}</p>
        <span class="text-[11px] text-slate-400 mt-1 block">VCard contact downloads</span>
    </div>
</div>

<!-- Secondary Actions Stats -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
        <span class="text-[11px] font-semibold text-slate-500 uppercase block">WhatsApp Clicks</span>
        <span class="text-xl font-bold text-slate-800">{{ number_format($stats['whatsapp_clicks'] ?? 0) }}</span>
    </div>
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
        <span class="text-[11px] font-semibold text-slate-500 uppercase block">Phone Clicks</span>
        <span class="text-xl font-bold text-slate-800">{{ number_format($stats['phone_clicks'] ?? 0) }}</span>
    </div>
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
        <span class="text-[11px] font-semibold text-slate-500 uppercase block">Email Clicks</span>
        <span class="text-xl font-bold text-slate-800">{{ number_format($stats['email_clicks'] ?? 0) }}</span>
    </div>
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
        <span class="text-[11px] font-semibold text-slate-500 uppercase block">Website Clicks</span>
        <span class="text-xl font-bold text-slate-800">{{ number_format($stats['website_clicks'] ?? 0) }}</span>
    </div>
</div>

<!-- Daily Scans Chart -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <h3 class="text-base font-bold text-slate-800 mb-4"><i class="fa-solid fa-chart-line text-sky-600 mr-2"></i> Daily Scan Traffic Trend</h3>
    <div class="h-64">
        <canvas id="dailyScansChart"></canvas>
    </div>
</div>

<!-- Breakdown Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Top Countries -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 mb-4"><i class="fa-solid fa-globe text-indigo-500 mr-2"></i> Top Countries</h3>
        <ul class="space-y-3">
            @forelse($stats['countries'] as $country => $count)
            <li class="flex items-center justify-between text-xs">
                <span class="font-medium text-slate-700">{{ $country }}</span>
                <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">{{ number_format($count) }}</span>
            </li>
            @empty
            <li class="text-xs text-slate-400 italic">No country data recorded yet</li>
            @endforelse
        </ul>
    </div>

    <!-- Top Referrers -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 mb-4"><i class="fa-solid fa-link text-emerald-500 mr-2"></i> Top Referrers</h3>
        <ul class="space-y-3">
            @forelse($stats['referrers'] as $referrer => $count)
            <li class="flex items-center justify-between text-xs">
                <span class="font-medium text-slate-700 truncate max-w-[180px]">{{ $referrer }}</span>
                <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">{{ number_format($count) }}</span>
            </li>
            @empty
            <li class="text-xs text-slate-400 italic">No referrer data recorded yet</li>
            @endforelse
        </ul>
    </div>

    <!-- Top Links Clicked -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 mb-4"><i class="fa-solid fa-arrow-pointer text-amber-500 mr-2"></i> Top Links Clicked</h3>
        <ul class="space-y-3">
            @forelse($stats['top_links'] as $link)
            <li class="flex items-center justify-between text-xs">
                <span class="font-medium text-slate-700 truncate max-w-[180px]">{{ $link['title'] }}</span>
                <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">{{ number_format($link['clicks']) }}</span>
            </li>
            @empty
            <li class="text-xs text-slate-400 italic">No link clicks recorded yet</li>
            @endforelse
        </ul>
    </div>
</div>

<!-- Device & OS Distribution -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-bold text-slate-800 mb-4"><i class="fa-solid fa-mobile-screen text-purple-500 mr-2"></i> Device Distribution</h3>
        <div class="h-48">
            <canvas id="deviceChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-bold text-slate-800 mb-4"><i class="fa-brands fa-chrome text-blue-500 mr-2"></i> Browser Breakdown</h3>
        <div class="h-48">
            <canvas id="browserChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daily Scans Line Chart
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

        // Device Doughnut Chart
        const deviceData = @json($stats['devices']);
        new Chart(document.getElementById('deviceChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(deviceData).length ? Object.keys(deviceData) : ['None'],
                datasets: [{
                    data: Object.values(deviceData).length ? Object.values(deviceData) : [1],
                    backgroundColor: ['#0284c7', '#6366f1', '#a855f7', '#ec4899', '#f59e0b']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Browser Bar Chart
        const browserData = @json($stats['browsers']);
        new Chart(document.getElementById('browserChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(browserData).length ? Object.keys(browserData) : ['None'],
                datasets: [{
                    label: 'Scans',
                    data: Object.values(browserData).length ? Object.values(browserData) : [0],
                    backgroundColor: '#6366f1'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    });
</script>
@endsection
