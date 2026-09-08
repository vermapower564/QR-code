<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profile->name }} {{ $profile->designation ? '| ' . $profile->designation : '' }}</title>

    <!-- SEO & Social OpenGraph -->
    <meta name="description" content="{{ Str::limit($profile->bio ?? 'Digital social profile for ' . $profile->name, 150) }}">
    <link rel="canonical" href="{{ url('/p/' . $profile->slug) }}" />
    <meta property="og:title" content="{{ $profile->name }}">
    <meta property="og:description" content="{{ Str::limit($profile->bio ?? 'View contact details and social links for ' . $profile->name, 150) }}">
    <meta property="og:url" content="{{ url('/p/' . $profile->slug) }}">
    <meta property="og:type" content="profile">
    @if($profile->profile_image)
        <meta property="og:image" content="{{ asset('storage/' . $profile->profile_image) }}">
    @endif


    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    @php
        $theme = $profile->theme_data ?? [];
        $preset = $theme['theme_preset'] ?? 'Classic';
        $bgColor = $theme['bg_color'] ?? '#f8fafc';
        $textColor = $theme['text_color'] ?? '#0f172a';
        $buttonStyle = $theme['button_style'] ?? 'rounded-2xl';

        $isDark = ($preset === 'Dark' || $bgColor === '#0f172a');
    @endphp

    <style>
        body {
            background-color: {{ $bgColor }};
            color: {{ $textColor }};
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased flex flex-col justify-between p-4 sm:p-6">
    <div class="max-w-md w-full mx-auto pt-6 pb-12">
        <!-- Header / Avatar -->
        <div class="text-center mb-6">
            @if($profile->profile_image)
                <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="{{ $profile->name }}" class="w-28 h-28 rounded-full object-cover mx-auto shadow-lg border-4 border-white mb-4"/>
            @else
                <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-3xl flex items-center justify-center mx-auto shadow-lg mb-4">
                    {{ strtoupper(substr($profile->name, 0, 1)) }}
                </div>
            @endif

            <h1 class="text-2xl font-black tracking-tight">{{ $profile->name }}</h1>
            @if($profile->designation || $profile->company)
                <p class="text-sm font-semibold opacity-80 mt-0.5">
                    {{ $profile->designation }} {{ ($profile->designation && $profile->company) ? '•' : '' }} {{ $profile->company }}
                </p>
            @endif

            @if($profile->bio)
                <p class="text-sm opacity-70 mt-3 max-w-xs mx-auto leading-relaxed">{{ $profile->bio }}</p>
            @endif
        </div>

        <!-- Quick Action Buttons -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-8">
            @if($profile->phone)
                <a href="tel:{{ $profile->phone }}" class="bg-white/10 backdrop-blur-md border border-white/20 p-3 {{ $buttonStyle }} text-center hover:scale-105 transition shadow-sm">
                    <i class="fa-solid fa-phone text-emerald-500 text-lg block mb-1"></i>
                    <span class="text-xs font-bold block">Call</span>
                </a>
            @endif

            @if($profile->email)
                <a href="mailto:{{ $profile->email }}" class="bg-white/10 backdrop-blur-md border border-white/20 p-3 {{ $buttonStyle }} text-center hover:scale-105 transition shadow-sm">
                    <i class="fa-solid fa-envelope text-sky-500 text-lg block mb-1"></i>
                    <span class="text-xs font-bold block">Email</span>
                </a>
            @endif

            @if($profile->website)
                <a href="{{ $profile->website }}" target="_blank" rel="noopener noreferrer" class="bg-white/10 backdrop-blur-md border border-white/20 p-3 {{ $buttonStyle }} text-center hover:scale-105 transition shadow-sm">
                    <i class="fa-solid fa-globe text-sky-500 text-lg block mb-1"></i>
                    <span class="text-xs font-bold block">Website</span>
                </a>
            @endif

            @if($profile->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $profile->phone) }}" target="_blank" class="bg-white/10 backdrop-blur-md border border-white/20 p-3 {{ $buttonStyle }} text-center hover:scale-105 transition shadow-sm">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-lg block mb-1"></i>
                    <span class="text-xs font-bold block">WhatsApp</span>
                </a>
            @endif

            <a href="{{ route('profile.contact', $profile->slug) }}" class="bg-white/10 backdrop-blur-md border border-white/20 p-3 {{ $buttonStyle }} text-center hover:scale-105 transition shadow-sm col-span-2 sm:col-span-1">
                <i class="fa-solid fa-address-book text-indigo-500 text-lg block mb-1"></i>
                <span class="text-xs font-bold block">Save VCF</span>
            </a>
        </div>

        <!-- Save Contact Card Banner -->
        <div class="mb-8">
            <a href="{{ route('profile.contact', $profile->slug) }}" class="w-full py-4 px-6 bg-gradient-to-r from-sky-600 to-indigo-600 text-white font-bold text-base {{ $buttonStyle }} flex items-center justify-center gap-3 shadow-lg shadow-sky-500/20 hover:opacity-95 transition">
                <i class="fa-solid fa-user-plus text-lg"></i>
                <span>Save Contact to Phone</span>
            </a>
        </div>

        <!-- Social Media Links -->
        @if($profile->socialLinks->count() > 0)
            <div class="space-y-3 mb-8">
                <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 mb-2 px-1">Social Networks</h3>
                @foreach($profile->socialLinks as $link)
                    @php
                        $iconClass = match(strtolower($link->platform)) {
                            'instagram' => 'fa-brands fa-instagram text-pink-500',
                            'facebook' => 'fa-brands fa-facebook text-blue-600',
                            'linkedin' => 'fa-brands fa-linkedin text-blue-700',
                            'youtube' => 'fa-brands fa-youtube text-red-600',
                            'twitter', 'x' => 'fa-brands fa-x-twitter',
                            'whatsapp' => 'fa-brands fa-whatsapp text-emerald-500',
                            'github' => 'fa-brands fa-github',
                            'tiktok' => 'fa-brands fa-tiktok',
                            default => 'fa-solid fa-link text-sky-500',
                        };
                    @endphp
                    <a href="{{ route('profile.click', ['profile' => $profile->id, 'link' => $link->id]) }}?type=social" target="_blank" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                        <div class="flex items-center gap-3">
                            <i class="{{ $iconClass }} text-xl w-6"></i>
                            <span>{{ $link->title ?: ucfirst($link->platform) }}</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Custom Links -->
        @if($profile->customLinks->count() > 0)
            <div class="space-y-3 mb-8">
                <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 mb-2 px-1">Featured Links</h3>
                @foreach($profile->customLinks as $link)
                    <a href="{{ route('profile.click', ['profile' => $profile->id, 'link' => $link->id]) }}?type=custom" target="_blank" class="w-full p-4 bg-slate-900 text-white {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-md hover:bg-slate-800 transition">
                        <div>
                            <span class="block">{{ $link->title }}</span>
                            @if($link->description)
                                <span class="text-xs font-normal opacity-75 block mt-0.5">{{ $link->description }}</span>
                            @endif
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-70"></i>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <footer class="text-center py-6 border-t border-black/5 text-xs opacity-60">
        Powered by <a href="/" class="font-bold underline">QR Identity</a>
    </footer>
</body>
</html>
