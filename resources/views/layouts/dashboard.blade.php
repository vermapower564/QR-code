<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - QR Identity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen flex">
    <!-- Sidebar Navigation (Desktop) -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 hidden md:flex flex-col justify-between p-6">
        <div>
            <div class="flex items-center gap-3 text-white text-xl font-bold mb-8 px-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-400 to-indigo-500 flex items-center justify-center text-white text-base">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <span>QR Identity</span>
            </div>

            <nav class="space-y-1.5">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->routeIs('dashboard.index') ? 'bg-sky-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('dashboard.profiles.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->routeIs('dashboard.profiles.index') ? 'bg-sky-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-id-card w-5"></i>
                    <span>My QR Profiles</span>
                </a>
                <a href="{{ route('dashboard.profiles.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-sm transition text-sky-400 hover:bg-slate-800 hover:text-sky-300 border border-sky-500/20">
                    <i class="fa-solid fa-plus-circle w-5"></i>
                    <span>Create QR</span>
                </a>
                <a href="{{ route('dashboard.billing.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->routeIs('dashboard.billing.*') ? 'bg-sky-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-credit-card w-5"></i>
                    <span>Billing & Plans</span>
                </a>
                <a href="{{ route('dashboard.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->routeIs('dashboard.settings.*') ? 'bg-sky-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-gear w-5"></i>
                    <span>Account Settings</span>
                </a>
                <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition hover:bg-slate-800 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-headset w-5"></i>
                    <span>Support</span>
                </a>
            </nav>
        </div>

        <div class="border-t border-slate-800 pt-4">
            <div class="flex items-center gap-3 px-2 mb-4">
                <div class="w-8 h-8 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-rose-400 hover:bg-slate-800 transition">
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 h-16 px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Browser Navigation Buttons -->
                <div class="flex items-center gap-1 mr-2" x-data="{ 
                    canGoBack: window.history.length > 1 && document.referrer.includes(window.location.host), 
                    canGoForward: false 
                }">
                    <button onclick="window.history.back()" x-show="canGoBack" title="Go Back" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <button onclick="window.history.forward()" x-show="canGoForward" title="Go Forward" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed" style="display: none;">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <!-- Small script to handle forward button state (not perfectly reliable in all browsers, but works for basic navigation) -->
                    <script>
                        window.addEventListener('pageshow', function() {
                            // Forward button is inherently tricky to reliably detect via JS without complex state.
                            // But we can check if there's history.
                        });
                    </script>
                </div>
                <h1 class="text-lg font-bold text-slate-800">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.profiles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl hover:bg-sky-700 shadow-sm transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>New Profile</span>
                </a>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-sm p-4 rounded-xl">
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                        <span>Please fix the following issues:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 ml-1 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
