<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Session Expired (419) - QR Identity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200 text-center space-y-6">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-3xl mx-auto shadow-sm">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>

        <div>
            <span class="inline-block px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-wider mb-2">Error 419 &bull; Security Refresh</span>
            <h1 class="text-2xl font-black text-slate-900">Page Session Expired</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Your page was open for a while, so your security token was refreshed to keep your account safe.
            </p>
        </div>

        <div class="p-4 bg-sky-50 rounded-2xl border border-sky-100 text-left flex items-start gap-3">
            <i class="fa-solid fa-shield-check text-sky-600 text-base mt-0.5 shrink-0"></i>
            <p class="text-xs text-slate-600">
                Click <strong>"Go Back &amp; Submit Again"</strong> below. Your browser will return to your form with your inputs saved and a fresh security token.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button onclick="window.history.back()" type="button" class="flex-1 py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl shadow-md shadow-sky-600/25 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Go Back &amp; Submit Again
            </button>
            <a href="/" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition flex items-center justify-center">
                Home
            </a>
        </div>
    </div>
</body>
</html>