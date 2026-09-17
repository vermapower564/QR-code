<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(app()->environment('production') || request()->isSecure())
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    <title>@yield('title', 'Dynamic QR Social Profile SaaS')</title>
    <!-- Vite Asset Pipeline with Tailwind CDN Fallback -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- QR Code Generator Client Library (CDN + Local Fallback) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Smart Navigation Helper -->
    <script src="{{ asset('js/navigation.js') }}"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-sky-500 selection:text-white">
    <!-- Global Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="/" class="flex items-center gap-2 text-xl font-bold text-slate-900 tracking-tight">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-sm">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <span>QR Identity</span>
                </a>

                <!-- Back & Next Navigation Buttons in Header -->
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

                <!-- Active Page Indicator (Changes from black to blue when page opens) -->
                @php
                    $activePageName = 'Home';
                    if (request()->routeIs('features')) $activePageName = 'Features';
                    elseif (request()->routeIs('pricing')) $activePageName = 'Pricing';
                    elseif (request()->routeIs('how-it-works')) $activePageName = 'How It Works';
                    elseif (request()->routeIs('about')) $activePageName = 'About';
                    elseif (request()->routeIs('contact')) $activePageName = 'Contact';
                    elseif (request()->routeIs('login')) $activePageName = 'Login';
                    elseif (request()->routeIs('register')) $activePageName = 'Register';
                    elseif (request()->routeIs('terms')) $activePageName = 'Terms';
                    elseif (request()->routeIs('privacy')) $activePageName = 'Privacy';
                @endphp
                <div class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 text-xs font-black shadow-sm tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <span>{{ $activePageName }}</span>
                </div>
            </div>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-bold">
                <a href="/" class="transition py-1 {{ request()->is('/') ? 'text-sky-600 font-black border-b-2 border-sky-600' : 'text-slate-600 hover:text-slate-900' }}">Home</a>
                <a href="{{ route('features') }}" class="transition py-1 {{ request()->routeIs('features') ? 'text-sky-600 font-black border-b-2 border-sky-600' : 'text-slate-600 hover:text-slate-900' }}">Features</a>
                <a href="{{ route('pricing') }}" class="transition py-1 {{ request()->routeIs('pricing') ? 'text-sky-600 font-black border-b-2 border-sky-600' : 'text-slate-600 hover:text-slate-900' }}">Pricing</a>
                <a href="{{ route('how-it-works') }}" class="transition py-1 {{ request()->routeIs('how-it-works') ? 'text-sky-600 font-black border-b-2 border-sky-600' : 'text-slate-600 hover:text-slate-900' }}">How It Works</a>
            </nav>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login', ['auth_required' => 1]) }}" class="text-sm font-bold text-slate-700 hover:text-sky-600 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-lock text-sky-600"></i> Login to Dashboard
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl hover:bg-sky-700 shadow-sm transition">
                        Create Your QR
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

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

    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} QR Identity SaaS. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
