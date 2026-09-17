<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'QR Identity'))</title>

    <!-- Meta Description & SEO Defaults -->
    <meta name="description" content="@yield('meta_description', 'Dynamic QR Social Profile SaaS - One QR Code for All Your Digital Identity')">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- Vite Asset Pipeline with Tailwind CDN Fallback -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Smart Navigation Helper -->
    <script src="{{ asset('js/navigation.js') }}"></script>

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between">
    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-xl font-bold text-slate-900 tracking-tight">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-sm">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <span>{{ config('app.name', 'QR Identity') }}</span>
                </a>

                <!-- Back & Next Navigation Buttons -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold gap-1 shadow-inner">
                    <button type="button" onclick="navigateApp('back')" title="Go Back" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white transition shadow-none hover:shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span class="hidden sm:inline">Back</span>
                    </button>
                    <span class="h-3.5 w-px bg-slate-300"></span>
                    <button type="button" onclick="navigateApp('next')" title="Go Next" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white transition shadow-none hover:shadow-sm">
                        <span class="hidden sm:inline">Next</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="px-4 py-2 text-sm font-semibold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900 transition">Sign in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl hover:bg-sky-700 shadow-sm transition">
                        Create Your QR
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs space-x-4">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'QR Identity') }}. All rights reserved.</span>
            <a href="{{ route('terms') }}" class="hover:underline">Terms</a>
            <a href="{{ route('privacy') }}" class="hover:underline">Privacy</a>
            <a href="{{ route('cookie-policy') }}" class="hover:underline">Cookies</a>
        </div>
    </footer>

    <!-- Floating Back & Next Navigation Widget -->
    <div class="fixed bottom-6 right-6 z-40 flex items-center gap-1.5 bg-slate-900/90 text-white backdrop-blur-md px-2.5 py-2 rounded-2xl shadow-2xl border border-slate-700/80">
        <button type="button" onclick="navigateApp('back')" title="Go Back" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800 transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Back</span>
        </button>
        <span class="h-4 w-px bg-slate-700"></span>
        <button type="button" onclick="navigateApp('next')" title="Go Next" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800 transition">
            <span>Next</span>
            <i class="fa-solid fa-arrow-right text-[11px]"></i>
        </button>
    </div>

    @stack('scripts')
</body>
</html>
