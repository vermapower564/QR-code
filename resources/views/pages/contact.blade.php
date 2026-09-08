@extends('layouts.public')

@section('title', 'Contact Support - QR Identity')

@section('content')
<section class="py-20 bg-slate-50">
    <div class="max-w-2xl mx-auto px-4 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Get in Touch</h1>
        <p class="text-sm text-slate-500 mb-8">Have questions about our platform or enterprise plans? Send us a message.</p>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Name</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm outline-none"/>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm outline-none"/>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subject</label>
                <input type="text" name="subject" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm outline-none"/>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Message</label>
                <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm outline-none"></textarea>
            </div>
            <button type="submit" class="w-full py-3.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl transition">Send Message</button>
        </form>
    </div>
</section>
@endsection
