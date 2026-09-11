@extends('layouts.public')

@section('title', 'Your QR Profile is Ready - QR Identity')

@section('content')
<div class="min-h-[85vh] bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full bg-white p-8 sm:p-10 rounded-3xl shadow-2xl border border-slate-200 text-center">
        <!-- Success Badge -->
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 font-black text-2xl flex items-center justify-center mx-auto shadow-sm mb-4">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1 class="text-3xl font-black text-slate-900">Your QR profile is ready.</h1>
        <p class="text-lg font-bold text-sky-600 mt-1">{{ $profile->name }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Your digital profile is now live at <a href="{{ url('/p/' . $profile->slug) }}" target="_blank" class="font-bold underline text-slate-700">{{ url('/p/' . $profile->slug) }}</a></p>

        <!-- Dynamic QR Preview Card -->
        <div class="my-8 bg-slate-50 p-6 rounded-2xl border border-slate-200 inline-block shadow-inner">
            <div class="bg-white p-4 rounded-xl shadow-md inline-block">
                @if($profile->qrCode && $profile->qrCode->file_path)
                    <img src="{{ asset('storage/' . $profile->qrCode->file_path) }}" alt="Dynamic QR Code" class="w-48 h-48 mx-auto"/>
                @else
                    <div class="w-48 h-48 mx-auto bg-slate-100 flex items-center justify-center rounded-xl">
                        <i class="fa-solid fa-qrcode text-4xl text-slate-300"></i>
                    </div>
                @endif
            </div>
            <div class="mt-3 text-xs font-semibold text-slate-500">
                <i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Dynamic QR Code (Encodes: <span class="font-mono text-slate-700">{{ url('/p/' . $profile->slug) }}</span>)
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6" x-data="{ copied: false }">
            <a href="{{ route('profile.show', $profile->slug) }}" target="_blank" class="w-full py-3 px-4 bg-slate-900 text-white font-bold rounded-xl text-sm hover:bg-slate-800 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Profile
            </a>

            <a href="{{ route('dashboard.profiles.qr.download', ['id' => $profile->id, 'format' => 'png']) }}" class="w-full py-3 px-4 bg-emerald-600 text-white font-bold rounded-xl text-sm hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-download"></i> Download QR
            </a>

            <button type="button" @click="navigator.clipboard.writeText('{{ url('/p/' . $profile->slug) }}'); copied = true; setTimeout(() => copied = false, 3000)" class="w-full py-3 px-4 bg-slate-100 text-slate-800 font-bold rounded-xl text-sm hover:bg-slate-200 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-share-nodes text-sky-600"></i>
                <span x-text="copied ? 'Link Copied!' : 'Share QR Link'"></span>
            </button>

            <a href="{{ route('dashboard.index') }}" class="w-full py-3 px-4 bg-sky-600 text-white font-bold rounded-xl text-sm hover:bg-sky-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-gauge-high"></i> Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
