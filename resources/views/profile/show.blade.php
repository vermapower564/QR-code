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

                <!-- Connect Section -->
        <div class="space-y-3 mb-8">
            <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 mb-2 px-1">Connect</h3>

            @if($profile->website)
                <a href="{{ $profile->website }}" target="_blank" rel="noopener noreferrer" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-globe text-sky-500 text-xl w-6"></i>
                        <span>Website</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @php
                $socials = $profile->socialLinks->keyBy(function($item) { return strtolower($item->platform); });
            @endphp

            @if($socials->has('instagram'))
                <a href="{{ $socials->get('instagram')->url }}" target="_blank" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-instagram text-pink-500 text-xl w-6"></i>
                        <span>Instagram</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @if($socials->has('facebook'))
                <a href="{{ $socials->get('facebook')->url }}" target="_blank" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-facebook text-blue-600 text-xl w-6"></i>
                        <span>Facebook</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @if($socials->has('linkedin'))
                <a href="{{ $socials->get('linkedin')->url }}" target="_blank" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-linkedin text-blue-700 text-xl w-6"></i>
                        <span>LinkedIn</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @if($socials->has('youtube'))
                <a href="{{ $socials->get('youtube')->url }}" target="_blank" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-youtube text-red-600 text-xl w-6"></i>
                        <span>YouTube</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @if($profile->whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $profile->whatsapp) }}" target="_blank" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-whatsapp text-emerald-500 text-xl w-6"></i>
                        <span>WhatsApp</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @if($profile->email)
                <a href="mailto:{{ $profile->email }}" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-indigo-500 text-xl w-6"></i>
                        <span>Email</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            @if($profile->phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $profile->phone) }}" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-emerald-600 text-xl w-6"></i>
                        <span>Phone</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
                </a>
            @endif

            <a href="{{ route('profile.contact', $profile->slug) }}" class="w-full p-4 bg-white/80 border border-slate-200/80 {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-sm hover:translate-y-[-2px] transition text-slate-900">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-plus text-sky-600 text-xl w-6"></i>
                    <span>Save Contact</span>
                </div>
                <i class="fa-solid fa-download text-xs opacity-40"></i>
            </a>
        </div>

        @php
            $otherSocials = $profile->socialLinks->filter(function($link) {
                return !in_array(strtolower($link->platform), ['instagram', 'facebook', 'linkedin', 'youtube']);
            });
        @endphp

        <!-- Other Social Media Links -->
        @if($otherSocials->count() > 0)
            <div class="space-y-3 mb-8">
                <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 mb-2 px-1">Other Socials</h3>
                @foreach($otherSocials as $link)
                    @php
                        $iconClass = match(strtolower($link->platform)) {
                            'website' => 'fa-solid fa-globe text-sky-500',
                            'twitter', 'x' => 'fa-brands fa-x-twitter',
                            'whatsapp' => 'fa-brands fa-whatsapp text-emerald-500',
                            'github' => 'fa-brands fa-github',
                            'tiktok' => 'fa-brands fa-tiktok',
                            'threads' => 'fa-brands fa-threads',
                            'pinterest' => 'fa-brands fa-pinterest text-red-600',
                            'snapchat' => 'fa-brands fa-snapchat text-yellow-400',
                            'telegram' => 'fa-brands fa-telegram text-blue-500',
                            'discord' => 'fa-brands fa-discord text-indigo-500',
                            'spotify' => 'fa-brands fa-spotify text-green-500',
                            'behance' => 'fa-brands fa-behance text-blue-600',
                            'dribbble' => 'fa-brands fa-dribbble text-pink-500',
                            'medium' => 'fa-brands fa-medium',
                            'reddit' => 'fa-brands fa-reddit text-orange-600',
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
                <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 mb-2 px-1">More Links</h3>
                @foreach($profile->customLinks->sortBy('sort_order') as $link)
                    <a href="{{ route('profile.click', ['profile' => $profile->id, 'link' => $link->id]) }}?type=custom" target="_blank" class="w-full p-4 bg-slate-900 text-white {{ $buttonStyle }} flex items-center justify-between font-bold text-sm shadow-md hover:bg-slate-800 transition group">
                        <div class="flex items-center gap-3">
                            @if($link->icon)
                                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white/90 group-hover:bg-white/20 transition-colors">
                                    <i class="{{ $link->icon }} text-sm"></i>
                                </div>
                            @endif
                            <div>
                                <span class="block">{{ $link->title }}</span>
                                @if($link->description)
                                    <span class="text-xs font-normal opacity-75 block mt-0.5">{{ $link->description }}</span>
                                @endif
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-70 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </a>
                @endforeach
            </div>
        @endif
        
        <!-- Lead Capture Form -->
        @if($profile->enable_lead_capture)
            <div class="space-y-3 mb-8 bg-white/80 p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-xs uppercase font-bold tracking-widest opacity-60 mb-2 text-slate-800">Contact Me</h3>
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm font-medium mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('profile.lead', $profile->slug) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="name" required placeholder="Your Name" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-900 bg-white">
                    </div>
                    <div>
                        <input type="email" name="email" required placeholder="Your Email" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-900 bg-white">
                    </div>
                    <div>
                        <input type="text" name="phone" placeholder="Your Phone (Optional)" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-900 bg-white">
                    </div>
                    <div>
                        <textarea name="message" rows="2" placeholder="Message (Optional)" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-slate-900 bg-white"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition shadow-md">
                        Submit
                    </button>
                </form>
            </div>
        @endif

        <!-- Share Profile Button -->
        <button onclick="shareProfile()" class="w-full mt-6 py-4 bg-slate-100 text-slate-800 {{ $buttonStyle }} font-bold text-sm hover:bg-slate-200 transition flex items-center justify-center gap-2 shadow-sm">
            <i class="fa-solid fa-share-nodes"></i> Share This Profile
        </button>

        <script>
            function shareProfile() {
                if (navigator.share) {
                    navigator.share({
                        title: '{{ $profile->name }}',
                        text: '{{ Str::limit($profile->bio ?? "Check out my profile", 100) }}',
                        url: window.location.href,
                    }).catch(console.error);
                } else {
                    navigator.clipboard.writeText(window.location.href);
                    alert('Profile URL copied to clipboard!');
                }
            }
        </script>
    </div>

    <footer class="text-center py-6 border-t border-black/5 text-xs opacity-60">
        Powered by <a href="/" class="font-bold underline">QR Identity</a>
    </footer>
</body>
</html>
