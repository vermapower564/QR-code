@extends('layouts.dashboard')

@section('title', 'Edit Profile - ' . $profile->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left 2 Cols: Form Editors -->
    <div class="lg:col-span-2 space-y-8">
        <!-- 1. Profile Information -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900">Profile Details</h3>
                <a href="{{ route('profile.show', $profile->slug) }}" target="_blank" class="text-xs font-bold text-sky-600 hover:underline">
                    Preview Public Page <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i>
                </a>
            </div>

            <form action="{{ route('dashboard.profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ phone: '{{ old('phone', $profile->phone) }}' }">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $profile->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username / Slug *</label>
                        <div class="flex rounded-xl border border-slate-300 overflow-hidden">
                            <span class="bg-slate-100 px-3 py-3 text-slate-500 text-xs font-mono border-r border-slate-300 flex items-center">/p/</span>
                            <input type="text" name="username" value="{{ old('username', $profile->slug) }}" required class="w-full px-3 py-3 text-sm outline-none"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation', $profile->designation) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company</label>
                        <input type="text" name="company" value="{{ old('company', $profile->company) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone</label>
                        <input type="text" name="phone" x-model="phone" @input="phone = phone.replace(/[^0-9]/g, '').slice(0, 10)" maxlength="10" pattern="[0-9]{10}" placeholder="9876543210" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none" :class="{'border-rose-400 focus:ring-rose-500': phone && phone.length !== 10}"/>
                        <p x-show="phone && phone.length !== 10" class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> Mobile number must be strictly 10 digits (0-9).
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $profile->email) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bio / Overview</label>
                    <textarea name="bio" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none">{{ old('bio', $profile->bio) }}</textarea>
                </div>

                <!-- Theme Style Selectors -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Background Hex</label>
                        <input type="color" name="bg_color" value="{{ $profile->theme_data['bg_color'] ?? '#f8fafc' }}" class="w-full h-10 rounded-lg cursor-pointer border border-slate-300"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Text Color Hex</label>
                        <input type="color" name="text_color" value="{{ $profile->theme_data['text_color'] ?? '#0f172a' }}" class="w-full h-10 rounded-lg cursor-pointer border border-slate-300"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Button Radius</label>
                        <select name="button_style" class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm">
                            <option value="rounded-none" {{ ($profile->theme_data['button_style'] ?? '') === 'rounded-none' ? 'selected' : '' }}>Square</option>
                            <option value="rounded-xl" {{ ($profile->theme_data['button_style'] ?? '') === 'rounded-xl' ? 'selected' : '' }}>Rounded (Default)</option>
                            <option value="rounded-full" {{ ($profile->theme_data['button_style'] ?? '') === 'rounded-full' ? 'selected' : '' }}>Pill Full</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="phone && phone.length !== 10" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 disabled:opacity-50 transition">Save Details</button>
                </div>
            </form>
        </div>

        <!-- 2. Social Links Manager -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Social Links</h3>

            <!-- Existing Social Links -->
            <div class="space-y-3 mb-6">
                @forelse($profile->socialLinks as $slink)
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-3">
                            <i class="fa-brands fa-{{ strtolower($slink->platform) }} text-lg w-5 text-slate-700"></i>
                            <div>
                                <span class="font-bold text-sm text-slate-900 block">{{ $slink->title }}</span>
                                <span class="text-xs text-slate-500 font-mono block">{{ $slink->url }}</span>
                            </div>
                        </div>

                        <form action="{{ route('dashboard.profiles.social.delete', ['profile' => $profile->id, 'link' => $slink->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-2"><i class="fa-solid fa-trash text-xs"></i></button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">No social links added yet.</p>
                @endforelse
            </div>

            <!-- Add Social Link Form -->
            <form action="{{ route('dashboard.profiles.social.add', $profile->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-100">
                @csrf
                <select name="platform" required class="px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold">
                    <option value="website">Website</option>
                    <option value="instagram">Instagram</option>
                    <option value="facebook">Facebook</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="twitter">X / Twitter</option>
                    <option value="youtube">YouTube</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="github">GitHub</option>
                    <option value="tiktok">TikTok</option>
                </select>

                <input type="url" name="url" placeholder="https://instagram.com/yourhandle" required class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none"/>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl font-bold text-sm hover:bg-sky-700 transition">Add Link</button>
            </form>
        </div>

        <!-- 3. Custom Action Buttons Manager -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Custom Featured Links / Buttons</h3>

            <div class="space-y-3 mb-6">
                @forelse($profile->customLinks as $clink)
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <span class="font-bold text-sm text-slate-900 block">{{ $clink->title }}</span>
                            <span class="text-xs text-slate-500 font-mono block">{{ $clink->url }}</span>
                        </div>
                        <form action="{{ route('dashboard.profiles.custom.delete', ['profile' => $profile->id, 'link' => $clink->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-2"><i class="fa-solid fa-trash text-xs"></i></button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">No custom buttons added yet.</p>
                @endforelse
            </div>

            <!-- Add Custom Link Form -->
            <form action="{{ route('dashboard.profiles.custom.add', $profile->id) }}" method="POST" class="space-y-3 pt-4 border-t border-slate-100">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="title" placeholder="Button Title (e.g. Book a Meeting)" required class="px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none"/>
                    <input type="url" name="url" placeholder="https://calendly.com/your-link" required class="px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none"/>
                </div>
                <input type="text" name="description" placeholder="Optional short subtitle" class="w-full px-4 py-2 rounded-xl border border-slate-300 text-xs outline-none"/>
                <button type="submit" class="w-full py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition">Add Custom Button</button>
            </form>
        </div>
    </div>

    <!-- Right Col: Quick QR Preview Card -->
    <div class="space-y-6">
        <div class="bg-slate-900 text-white p-6 rounded-3xl shadow-xl flex flex-col items-center text-center sticky top-24">
            <h4 class="font-bold text-sm text-slate-300 mb-4">DYNAMIC QR CODE</h4>
            
            <div class="bg-white p-4 rounded-2xl mb-4 shadow-md">
                @if($profile->qrCode && $profile->qrCode->file_path)
                    <img src="{{ asset('storage/' . $profile->qrCode->file_path) }}" alt="QR Code" class="w-44 h-44 object-contain"/>
                @else
                    <div class="w-44 h-44 bg-slate-100 flex items-center justify-center text-slate-400 font-mono text-xs">Generating...</div>
                @endif
            </div>

            <p class="font-bold text-base text-white">{{ $profile->name }}</p>
            <p class="text-xs text-sky-400 font-mono mt-1">/p/{{ $profile->slug }}</p>

            <a href="{{ route('dashboard.profiles.qr', $profile->id) }}" class="mt-6 w-full py-3 bg-sky-600 hover:bg-sky-500 font-bold text-sm rounded-xl transition">
                <i class="fa-solid fa-sliders mr-2"></i> Customize & Download QR
            </a>
        </div>
    </div>
</div>
@endsection
