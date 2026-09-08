<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Openings - {{ $profile->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased flex flex-col justify-between p-6">
    <div class="max-w-md w-full mx-auto my-auto py-12 text-center" x-data="{ copied: false }">
        <!-- Brand / Profile Avatar -->
        <div class="mb-6">
            @if($profile->profile_image)
                <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="{{ $profile->name }}" class="w-20 h-20 rounded-full object-cover mx-auto shadow-md border-2 border-white mb-3"/>
            @else
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-md mb-3">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
            @endif
            <p class="text-sm font-bold text-slate-600">{{ $profile->name }}</p>
        </div>

        <!-- Empty State Card -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xl">
            <div class="w-14 h-14 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-4">
                <i class="fa-solid fa-clock font-normal"></i>
            </div>

            <h2 class="text-xl font-black text-slate-900 tracking-tight">No openings at the moment.</h2>

            <p class="text-sm text-slate-500 mt-2 max-w-xs mx-auto leading-relaxed">
                There are currently no available time slots or schedule openings. Please check again later.
            </p>

            <div class="mt-8 space-y-3">
                <a href="{{ route('profile.show', $profile->slug) }}" class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl block shadow-md shadow-sky-500/20 transition">
                    <i class="fa-solid fa-arrow-left text-xs mr-2"></i> Back to Profile
                </a>

                <button @click="navigator.clipboard.writeText('{{ route('profile.show', $profile->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl block transition">
                    <i class="fa-solid fa-copy text-xs mr-2"></i>
                    <span x-text="copied ? 'Link Copied!' : 'Copy Profile Link'"></span>
                </button>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-xs text-slate-400">
        Powered by <a href="/" class="font-bold underline">QR Identity</a>
    </footer>
</body>
</html>
