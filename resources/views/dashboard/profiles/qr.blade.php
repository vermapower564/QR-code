@extends('layouts.dashboard')

@section('title', 'QR Studio - ' . $profile->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- QR Customization Form -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h2 class="text-xl font-bold text-slate-900">QR Code Customization Studio</h2>
                <p class="text-xs text-slate-500 mt-0.5">Style your QR code colors, shape, and watermark logo. Changes apply dynamically.</p>
            </div>

            <form action="{{ route('dashboard.profiles.qr.update', $profile->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Foreground Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="foreground_color" value="{{ $profile->qrCode->foreground_color ?? '#000000' }}" class="w-12 h-12 rounded-xl cursor-pointer border border-slate-300"/>
                            <span class="text-xs font-mono text-slate-600">{{ $profile->qrCode->foreground_color ?? '#000000' }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Background Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="background_color" value="{{ $profile->qrCode->background_color ?? '#ffffff' }}" class="w-12 h-12 rounded-xl cursor-pointer border border-slate-300"/>
                            <span class="text-xs font-mono text-slate-600">{{ $profile->qrCode->background_color ?? '#ffffff' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Module Style</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer font-bold text-xs hover:bg-slate-50">
                            <input type="radio" name="style" value="square" {{ ($profile->qrCode->style ?? 'square') === 'square' ? 'checked' : '' }}/>
                            <span>Square</span>
                        </label>
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer font-bold text-xs hover:bg-slate-50">
                            <input type="radio" name="style" value="rounded" {{ ($profile->qrCode->style ?? '') === 'rounded' ? 'checked' : '' }}/>
                            <span>Rounded</span>
                        </label>
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer font-bold text-xs hover:bg-slate-50">
                            <input type="radio" name="style" value="dots" {{ ($profile->qrCode->style ?? '') === 'dots' ? 'checked' : '' }}/>
                            <span>Dots</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Central Logo Watermark</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100"/>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-sky-600 text-white font-bold text-sm rounded-xl hover:bg-sky-700 transition">Regenerate QR Code</button>
                </div>
            </form>
        </div>

        <!-- Download Options -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Export & Download Options</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- PNG Download -->
                <div class="p-4 border border-slate-200 rounded-2xl text-center">
                    <i class="fa-solid fa-file-image text-sky-600 text-2xl mb-2"></i>
                    <h4 class="font-bold text-sm text-slate-900">PNG Image</h4>
                    <p class="text-xs text-slate-500 mb-4">High Resolution</p>

                    <div class="space-y-1.5">
                        <a href="{{ route('dashboard.profiles.qr.download', ['id' => $profile->id, 'format' => 'png', 'size' => 512]) }}" class="block w-full py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">512 x 512 px</a>
                        <a href="{{ route('dashboard.profiles.qr.download', ['id' => $profile->id, 'format' => 'png', 'size' => 1024]) }}" class="block w-full py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">1024 x 1024 px</a>
                        <a href="{{ route('dashboard.profiles.qr.download', ['id' => $profile->id, 'format' => 'png', 'size' => 2048]) }}" class="block w-full py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold">2048 x 2048 px</a>
                    </div>
                </div>

                <!-- SVG Vector -->
                <div class="p-4 border border-slate-200 rounded-2xl text-center flex flex-col justify-between">
                    <div>
                        <i class="fa-solid fa-vector-square text-indigo-600 text-2xl mb-2"></i>
                        <h4 class="font-bold text-sm text-slate-900">Vector SVG</h4>
                        <p class="text-xs text-slate-500 mb-4">Infinite Scalability</p>
                    </div>
                    <a href="{{ route('dashboard.profiles.qr.download', ['id' => $profile->id, 'format' => 'svg']) }}" class="block w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold">Download SVG</a>
                </div>

                <!-- Printable PDF Frame -->
                <div class="p-4 border border-slate-200 rounded-2xl text-center flex flex-col justify-between">
                    <div>
                        <i class="fa-solid fa-file-pdf text-rose-600 text-2xl mb-2"></i>
                        <h4 class="font-bold text-sm text-slate-900">Printable PDF</h4>
                        <p class="text-xs text-slate-500 mb-4">With SCAN ME Frame</p>
                    </div>
                    <a href="{{ route('dashboard.profiles.qr.download', ['id' => $profile->id, 'format' => 'pdf']) }}" class="block w-full py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold">Download PDF Frame</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Live QR Preview & Web Share -->
    <div class="space-y-6">
        <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl text-center">
            <h3 class="text-xs uppercase font-bold tracking-widest text-slate-400 mb-6">LIVE QR PREVIEW</h3>

            <div class="bg-white p-6 rounded-2xl inline-block shadow-lg mb-6">
                @if($profile->qrCode && $profile->qrCode->file_path)
                    <img src="{{ asset('storage/' . $profile->qrCode->file_path) }}" alt="QR Code" class="w-52 h-52 object-contain mx-auto"/>
                @endif
            </div>

            <p class="font-bold text-lg text-white">{{ $profile->name }}</p>
            <p class="text-xs text-sky-400 font-mono mt-1 break-all">{{ route('profile.show', $profile->slug) }}</p>

            <div class="mt-8 pt-6 border-t border-slate-800 space-y-3" x-data="{ copied: false }">
                <button @click="navigator.clipboard.writeText('{{ route('profile.show', $profile->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-copy"></i>
                    <span x-text="copied ? 'Copied!' : 'Copy Profile Link'"></span>
                </button>

                <a href="https://wa.me/?text={{ urlencode('Scan my QR profile: ' . route('profile.show', $profile->slug)) }}" target="_blank" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i> Share on WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
