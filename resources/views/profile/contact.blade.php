@extends('layouts.public')

@section('title', 'Save Contact - ' . $profile->name)

@section('content')
<div class="min-h-[75vh] bg-slate-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-200 text-center space-y-6">
        <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-md">
            {{ strtoupper(substr($profile->name, 0, 1)) }}
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-900">{{ $profile->name }}</h2>
            @if($profile->designation || $profile->company)
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    {{ $profile->designation }} {{ ($profile->designation && $profile->company) ? '•' : '' }} {{ $profile->company }}
                </p>
            @endif
            <p class="text-xs text-slate-400 mt-0.5">Click below to download contact card (.vcf)</p>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-left font-mono text-xs space-y-1 text-slate-700">
            <p><span class="font-bold text-slate-400">FN:</span> {{ $profile->name }}</p>
            @if($profile->phone)
                <p><span class="font-bold text-slate-400">TEL:</span> {{ $profile->phone }}</p>
            @endif
            @if($profile->email)
                <p><span class="font-bold text-slate-400">EMAIL:</span> {{ $profile->email }}</p>
            @endif
            @if($profile->website)
                <p><span class="font-bold text-slate-400">URL:</span> {{ $profile->website }}</p>
            @endif
        </div>

        <a href="{{ route('profile.contact', $profile->slug) }}?download=1" class="w-full py-4 bg-gradient-to-r from-sky-600 to-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg flex items-center justify-center gap-2">
            <i class="fa-solid fa-download"></i> Download VCard (.vcf)
        </a>

        <a href="{{ route('profile.show', $profile->slug) }}" class="block text-xs font-bold text-slate-500 hover:text-slate-900">
            &larr; Return to Public Profile
        </a>
    </div>
</div>
@endsection
