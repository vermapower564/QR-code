@extends('layouts.admin')

@section('title', 'Profile Templates')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-4">
        <h2 class="text-xl font-bold text-white mb-4">Profile Templates</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($templates as $tpl)
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base">{{ $tpl->name }}</h3>
                        <p class="text-xs text-slate-400 font-mono">Category: {{ $tpl->category }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $tpl->is_premium ? 'bg-amber-950 text-amber-300' : 'bg-slate-800 text-slate-300' }}">
                        {{ $tpl->is_premium ? 'Premium' : 'Free' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
        <h3 class="font-bold text-white text-base mb-4">Add Template</h3>

        <form action="{{ route('admin.templates.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">Template Name</label>
                <input type="text" name="name" required placeholder="Business Card" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">Slug</label>
                <input type="text" name="slug" required placeholder="business-card" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">Category</label>
                <input type="text" name="category" value="Business" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none"/>
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-300">
                <input type="checkbox" name="is_premium" value="1" class="rounded"/>
                <span>Is Premium Template?</span>
            </label>

            <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 font-bold text-xs text-white rounded-xl transition">Add Template</button>
        </form>
    </div>
</div>
@endsection
