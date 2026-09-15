@extends('layouts.public')

@section('title', '500 - Internal Server Error')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-6">
    <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-2xl mb-4">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-900">500 - Something Went Wrong</h2>
    <p class="text-slate-600 mt-2 max-w-md">An unexpected error occurred while processing your request. Please try again later.</p>

    @if(config('app.debug') || request()->query('debug') == '1')
        @if(isset($exception) && $exception)
            <div class="mt-6 p-5 bg-slate-900 text-rose-400 rounded-2xl text-left text-xs font-mono max-w-2xl w-full overflow-auto shadow-xl border border-slate-800">
                <p class="font-bold text-amber-400 mb-2 border-b border-slate-800 pb-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-bug"></i> Diagnostic Error Details:
                </p>
                <p class="mb-1"><span class="text-slate-400">Class:</span> {{ get_class($exception) }}</p>
                <p class="mb-1"><span class="text-slate-400">Message:</span> {{ $exception->getMessage() ?: 'No message provided' }}</p>
                <p><span class="text-slate-400">Location:</span> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
            </div>
        @endif
    @endif

    <div class="flex items-center gap-3 mt-6">
        <a href="/" class="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-sm shadow-md transition">Return to Home</a>
        @if(!request()->query('debug'))
            <a href="{{ request()->fullUrlWithQuery(['debug' => 1]) }}" class="px-4 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold text-xs transition">Show Error Details</a>
        @endif
    </div>
</div>
@endsection
