<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dynamic QR Social Profile SaaS')</title>
    <!-- Tailwind CSS CDN for instant rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-brand-500 selection:text-white">
    <!-- Global Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 text-xl font-bold text-slate-900 tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <span>QR Identity</span>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#features" class="hover:text-slate-900 transition">Features</a>
                <a href="#how-it-works" class="hover:text-slate-900 transition">How it works</a>
                <a href="#pricing" class="hover:text-slate-900 transition">Pricing</a>
            </nav>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900 transition">Sign in</a>
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

    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} QR Identity SaaS. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
