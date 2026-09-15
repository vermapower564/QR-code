<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Admin Panel') - QR Identity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Smart Navigation Helper -->
    <script src="{{ asset('js/navigation.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-950 text-slate-300 flex-shrink-0 p-6 flex flex-col justify-between border-r border-slate-800">
        <div>
            <div class="flex items-center gap-3 text-white text-xl font-bold mb-8 px-2">
                <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center justify-center text-white text-base">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <span>Admin Console</span>
            </div>

            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    <span>Overview</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.users.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5"></i>
                    <span>User Management</span>
                </a>
                <a href="{{ route('admin.profiles.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.profiles.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-qrcode w-5"></i>
                    <span>QR Profiles</span>
                </a>
                <a href="{{ route('admin.plans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.plans.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-tags w-5"></i>
                    <span>Subscription Plans</span>
                </a>
                <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.templates.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-palette w-5"></i>
                    <span>Profile Templates</span>
                </a>
                <a href="{{ route('admin.domains.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.domains.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-globe w-5"></i>
                    <span>Custom Domains</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.reports.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-file-csv w-5"></i>
                    <span>Reports & Exports</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-shield-halved w-5"></i>
                    <span>Audit Logs</span>
                </a>
            </nav>

        </div>

        <div>
            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-arrow-left"></i> Back to User Dashboard
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-slate-950 border-b border-slate-800 h-16 px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Back & Next Navigation Buttons -->
                <div class="flex items-center bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs font-bold gap-1 shadow-inner">
                    <button type="button" onclick="navigateApp('back')" title="Go Back" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span class="hidden sm:inline">Back</span>
                    </button>
                    <span class="h-3.5 w-px bg-slate-800"></span>
                    <button type="button" onclick="navigateApp('next')" title="Go Next" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        <span class="hidden sm:inline">Next</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
                <h1 class="text-lg font-bold text-white">@yield('title', 'Admin Panel')</h1>
            </div>
            <a href="{{ route('dashboard.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition flex items-center gap-1.5 bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-800">
                <i class="fa-solid fa-gauge"></i> <span>User Dashboard</span>
            </a>
        </header>

        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
            @if(session('success'))
                <div class="mb-6 bg-emerald-950 border border-emerald-800 text-emerald-300 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <!-- Floating Back & Next Navigation Widget -->
    <div class="fixed bottom-6 right-6 z-40 flex items-center gap-1.5 bg-slate-950/90 text-white backdrop-blur-md px-2.5 py-2 rounded-2xl shadow-2xl border border-slate-800">
        <button type="button" onclick="navigateApp('back')" title="Go Back" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800 transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Back</span>
        </button>
        <span class="h-4 w-px bg-slate-800"></span>
        <button type="button" onclick="navigateApp('next')" title="Go Next" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800 transition">
            <span>Next</span>
            <i class="fa-solid fa-arrow-right text-[11px]"></i>
        </button>
    </div>
</body>
</html>
