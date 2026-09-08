@extends('layouts.public')

@section('title', '404 - Page Not Found')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-6">
    <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-2xl mb-4">
        <i class="fa-solid fa-ghost"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-900">404 - Page Not Found</h2>
    <p class="text-slate-600 mt-2 max-w-md">The requested page or QR profile does not exist.</p>
    <a href="/" class="mt-6 px-6 py-3 bg-sky-600 text-white rounded-xl font-bold text-sm">Return to Home</a>
</div>
@endsection
