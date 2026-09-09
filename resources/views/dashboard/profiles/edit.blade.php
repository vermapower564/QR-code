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
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $profile->whatsapp) }}" placeholder="e.g. 1234567890" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Website URL</label>
                        <input type="url" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none"/>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Business Address (Get Directions)</label>
                        <textarea name="address" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none">{{ old('address', $profile->address) }}</textarea>
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
                            @php
                                $adminIconClass = match(strtolower($slink->platform)) {
                                    'website' => 'fa-solid fa-globe',
                                    'x', 'twitter' => 'fa-brands fa-x-twitter',
                                    default => 'fa-brands fa-' . strtolower($slink->platform),
                                };
                            @endphp
                            <i class="{{ $adminIconClass }} text-lg w-5 text-slate-700"></i>
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
                    <option value="tiktok">TikTok</option>
                    <option value="threads">Threads</option>
                    <option value="pinterest">Pinterest</option>
                    <option value="snapchat">Snapchat</option>
                    <option value="telegram">Telegram</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="github">GitHub</option>
                    <option value="discord">Discord</option>
                    <option value="spotify">Spotify</option>
                    <option value="behance">Behance</option>
                    <option value="dribbble">Dribbble</option>
                    <option value="medium">Medium</option>
                    <option value="reddit">Reddit</option>
                </select>

                <input type="url" name="url" placeholder="https://instagram.com/yourhandle" required class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none"/>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl font-bold text-sm hover:bg-sky-700 transition">Add Link</button>
            </form>
        </div>

        <!-- 3. Custom Action Buttons Manager -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50" 
             x-data="customLinksManager()">
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Custom Featured Links</h3>
                    <p class="text-xs text-slate-500 mt-1">Add links to your calendar, portfolio, or store. Drag to reorder.</p>
                </div>
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-link"></i>
                </div>
            </div>

            <!-- Existing Links (Sortable List) -->
            <div id="custom-links-list" class="space-y-3 mb-8 min-h-[50px]">
                @forelse($profile->customLinks()->orderBy('sort_order')->get() as $clink)
                    <div x-data="{ editing: false }" data-id="{{ $clink->id }}" class="group relative overflow-hidden bg-white border border-slate-200 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all mb-3">
                        <div x-show="!editing" class="flex items-center justify-between p-4 cursor-move">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-slate-200 group-hover:bg-indigo-400 transition-colors"></div>
                        
                        <div class="flex items-center gap-4 pl-2">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i class="{{ $clink->icon ?: 'fa-solid fa-link' }} text-lg"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-slate-900 block group-hover:text-indigo-600 transition-colors">{{ $clink->title }}</span>
                                @if($clink->description)
                                    <span class="text-xs text-slate-500 block mt-0.5">{{ $clink->description }}</span>
                                @endif
                                <a href="{{ $clink->url }}" target="_blank" class="text-[10px] font-mono text-sky-500 hover:underline mt-1 inline-block"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>{{ Str::limit($clink->url, 40) }}</a>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 opacity-100 sm:opacity-50 group-hover:opacity-100 transition-opacity">
                            <div class="px-2 py-1 bg-slate-100 text-slate-400 rounded cursor-grab active:cursor-grabbing hover:bg-slate-200 hover:text-slate-600" title="Drag to reorder">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </div>
                            <button @click="editing = true" type="button" class="w-8 h-8 flex items-center justify-center rounded bg-indigo-50 text-indigo-500 hover:bg-indigo-500 hover:text-white transition-colors" title="Edit Link">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <form action="{{ route('dashboard.profiles.custom.delete', ['profile' => $profile->id, 'link' => $clink->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors" title="Delete Link">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                        </div>

                        <!-- Edit Form -->
                        <div x-show="editing" style="display: none;" class="p-4 bg-slate-50 border-t border-slate-200">
                            <form action="{{ route('dashboard.profiles.custom.update', ['profile' => $profile->id, 'link' => $clink->id]) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">Title</label>
                                        <input type="text" name="title" value="{{ $clink->title }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">URL</label>
                                        <input type="url" name="url" value="{{ $clink->url }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-2">
                                    <button @click="editing = false" type="button" class="px-3 py-1.5 text-xs font-bold text-slate-500 hover:bg-slate-200 rounded-lg">Cancel</button>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200" id="empty-state">
                        <i class="fa-solid fa-link-slash text-slate-300 text-3xl mb-3"></i>
                        <p class="text-sm font-bold text-slate-500">No custom links added yet</p>
                        <p class="text-xs text-slate-400 mt-1">Add your first custom link below</p>
                    </div>
                @endforelse
            </div>

            <!-- Add Custom Link Form -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-100 to-transparent rounded-bl-full -z-0"></div>
                <h4 class="font-bold text-sm text-slate-800 mb-4 relative z-10"><i class="fa-solid fa-plus-circle text-indigo-500 mr-1.5"></i> Create New Button</h4>
                
                <form action="{{ route('dashboard.profiles.custom.add', $profile->id) }}" method="POST" class="space-y-4 relative z-10">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">Button Title</label>
                            <input type="text" name="title" placeholder="e.g. Book a Meeting" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">Destination URL</label>
                            <input type="url" name="url" placeholder="https://calendly.com/your-link" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">Subtitle / Description</label>
                            <input type="text" name="description" placeholder="e.g. Schedule a 30-min discovery call" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">Button Icon</label>
                            <select name="icon" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-semibold">
                                <option value="fa-solid fa-link">Default Link</option>
                                <option value="fa-regular fa-calendar-check">Calendar / Booking</option>
                                <option value="fa-solid fa-briefcase">Portfolio / Work</option>
                                <option value="fa-solid fa-cart-shopping">Store / Shop</option>
                                <option value="fa-solid fa-video">Video / Meet</option>
                                <option value="fa-solid fa-music">Music / Audio</option>
                                <option value="fa-solid fa-envelope">Newsletter / Email</option>
                                <option value="fa-solid fa-download">Download / File</option>
                                <option value="fa-solid fa-star">Featured / Star</option>
                                <option value="fa-solid fa-heart">Support / Donate</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 mt-2">
                        <i class="fa-solid fa-bolt mr-1.5"></i> Add Custom Link
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Add SortableJS library and initialization -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('customLinksManager', () => ({
                    init() {
                        const el = document.getElementById('custom-links-list');
                        if(el && el.children.length > 0 && !document.getElementById('empty-state')) {
                            Sortable.create(el, {
                                animation: 150,
                                ghostClass: 'bg-slate-50',
                                handle: '.cursor-grab',
                                onEnd: (evt) => {
                                    this.saveOrder();
                                }
                            });
                        }
                    },
                    saveOrder() {
                        const list = document.getElementById('custom-links-list');
                        const items = list.querySelectorAll('[data-id]');
                        const orders = {};
                        
                        items.forEach((item, index) => {
                            orders[item.getAttribute('data-id')] = index + 1;
                        });
                        
                        fetch('{{ route('dashboard.profiles.custom.reorder', $profile->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ orders: orders })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Order saved!', data);
                        })
                        .catch(err => {
                            console.error('Error saving order', err);
                        });
                    }
                }));
            });
        </script>
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
