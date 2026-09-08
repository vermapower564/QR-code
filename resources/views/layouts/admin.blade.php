<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - QR Identity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                <a href="{{ route('admin.plans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.plans.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-tags w-5"></i>
                    <span>Subscription Plans</span>
                </a>
                <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.templates.*') ? 'bg-purple-600 text-white' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-palette w-5"></i>
                    <span>Profile Templates</span>
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
            <h1 class="text-lg font-bold text-white">@yield('title', 'Admin Panel')</h1>
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
</body>
</html>
