@extends('layouts.dashboard')

@section('title', 'Bulk QR Import')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Bulk Profile Import & QR Generation</h1>
            <p class="text-xs text-slate-500 mt-1">Upload a CSV file to automatically create multiple profiles and generate dynamic QR codes asynchronously.</p>
        </div>
    </div>

    @if(!$hasFeature)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 p-6 rounded-3xl space-y-3">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-crown text-amber-600 text-xl"></i>
                <h3 class="text-base font-bold">Business Plan Feature</h3>
            </div>
            <p class="text-xs text-amber-700 leading-relaxed">
                Bulk profile CSV import and batch QR generation is available exclusively on the Business Plan. Upgrade your subscription to import team members and events in bulk.
            </p>
            <a href="{{ route('dashboard.billing.index') }}" class="inline-block px-4 py-2 bg-amber-600 text-white font-bold text-xs rounded-xl shadow">Upgrade to Business</a>
        </div>
    @else
        @if(isset($previewRows) && count($previewRows) > 0)
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">2. Confirm CSV Import Preview</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ count($previewRows) }} profile rows detected and ready for batch creation.</p>
                    </div>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Generated Slug</th>
                                <th class="p-3">Designation</th>
                                <th class="p-3">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                            @foreach($previewRows as $row)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-3 font-mono text-slate-400">{{ $row['row_number'] }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $row['name'] }}</td>
                                    <td class="p-3 font-mono text-sky-600">{{ $row['slug'] }}</td>
                                    <td class="p-3">{{ $row['designation'] ?: '-' }}</td>
                                    <td class="p-3">{{ $row['email'] ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('dashboard.profiles.bulk.process') }}" method="POST">
                    @csrf
                    <div class="flex items-center gap-3">
                        <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white font-bold text-sm rounded-xl shadow hover:bg-emerald-700 transition">
                            <i class="fa-solid fa-check me-2"></i> Confirm & Import All {{ count($previewRows) }} Profiles
                        </button>
                        <a href="{{ route('dashboard.profiles.bulk') }}" class="px-6 py-4 bg-slate-100 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-200 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">1. Upload CSV File</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ensure columns include: <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono">name, designation, company, email, phone, website</code></p>
                </div>

                <form action="{{ route('dashboard.profiles.bulk.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50 hover:bg-slate-100/60 transition">
                        <i class="fa-solid fa-file-csv text-4xl text-sky-600 mb-3 block"></i>
                        <input type="file" name="csv_file" accept=".csv,text/csv" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700"/>
                    </div>

                    <button type="submit" class="w-full py-4 bg-sky-600 text-white font-bold text-sm rounded-xl shadow hover:bg-sky-700 transition">
                        Upload & Preview CSV Rows
                    </button>
                </form>
            </div>
        @endif
    @endif
</div>
@endsection
