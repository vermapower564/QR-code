@extends('layouts.public')

@section('title', 'Dynamic QR Social Profile SaaS - Create Your Digital Identity')

@section('content')
<div x-data="homeExperience()" x-init="init()" class="relative overflow-hidden bg-gradient-to-b from-sky-50/60 via-white to-slate-50">

    <!-- Hero Header -->
    <section class="pt-16 pb-10 sm:pt-20 sm:pb-12 text-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-sky-100 text-sky-700 text-xs font-bold uppercase tracking-wider mb-6 shadow-sm">
            <i class="fa-solid fa-bolt text-sky-600"></i> Free Instant QR Generator & Dynamic Identity Platform
        </div>

        <h1 class="text-4xl sm:text-6xl font-black text-sky-600 tracking-tight max-w-4xl mx-auto leading-tight">
            Create your QR Code instantly. <br class="hidden sm:inline"/>
            <span class="bg-gradient-to-r from-sky-600 to-indigo-600 bg-clip-text text-transparent">Scan, connect & share in seconds.</span>
        </h1>

        <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto font-normal leading-relaxed">
            Anyone can create and download high-resolution QR codes immediately below. Or sign in with your email and password to manage dynamic profiles with real-time scan analytics.
        </p>
    </section>

    <!-- Main Interactive Card (QR Creator + Quick Login) -->
    <section id="interactive-section" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden">
            
            <!-- Tab Switcher Bar -->
            <div class="flex border-b border-slate-200 bg-slate-50/80 p-2 sm:p-3 gap-2">
                <button type="button" 
                        @click="setTab('qr')"
                        :class="activeTab === 'qr' ? 'bg-white text-sky-700 shadow-md font-extrabold border-slate-200' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="flex-1 py-3 px-4 rounded-2xl text-sm sm:text-base flex items-center justify-center gap-2.5 transition-all">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm" :class="activeTab === 'qr' ? 'bg-sky-100 text-sky-600' : 'bg-slate-200 text-slate-500'">
                        <i class="fa-solid fa-qrcode"></i>
                    </span>
                    <span>Create Instant QR</span>
                    <span class="hidden sm:inline-block text-[11px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Free</span>
                </button>

                <button type="button" 
                        id="login-card"
                        @click="setTab('login')"
                        :class="activeTab === 'login' ? 'bg-white text-sky-600 shadow-md font-extrabold border-slate-200' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="flex-1 py-3 px-4 rounded-2xl text-sm sm:text-base flex items-center justify-center gap-2.5 transition-all">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm" :class="activeTab === 'login' ? 'bg-sky-100 text-sky-600' : 'bg-slate-200 text-slate-500'">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <span>Login Box (Open Dashboard)</span>
                    @auth
                        <span class="hidden sm:inline-block text-[11px] font-bold uppercase px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">Active</span>
                    @endauth
                </button>
            </div>

            <!-- TAB 1: CREATE QR CODE (Anyone can use instantly) -->
            <div x-show="activeTab === 'qr'" x-transition.opacity.duration.250ms class="p-6 sm:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left: Inputs & QR Type -->
                    <div class="lg:col-span-7 space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1. Choose QR Code Type</label>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                <button type="button" @click="qrType = 'url'; updateQr()" :class="qrType === 'url' ? 'bg-sky-50 border-sky-600 text-sky-700 shadow-sm' : 'border-slate-200 hover:bg-slate-50 text-slate-700'" class="p-3 rounded-2xl border text-center font-bold text-xs flex flex-col items-center gap-1.5 transition">
                                    <i class="fa-solid fa-globe text-base text-sky-600"></i>
                                    <span>Website</span>
                                </button>
                                <button type="button" @click="qrType = 'profile'; updateQr()" :class="qrType === 'profile' ? 'bg-sky-50 border-sky-600 text-sky-700 shadow-sm' : 'border-slate-200 hover:bg-slate-50 text-slate-700'" class="p-3 rounded-2xl border text-center font-bold text-xs flex flex-col items-center gap-1.5 transition">
                                    <i class="fa-solid fa-id-badge text-base text-indigo-600"></i>
                                    <span>Bio Profile</span>
                                </button>
                                <button type="button" @click="qrType = 'vcard'; updateQr()" :class="qrType === 'vcard' ? 'bg-sky-50 border-sky-600 text-sky-700 shadow-sm' : 'border-slate-200 hover:bg-slate-50 text-slate-700'" class="p-3 rounded-2xl border text-center font-bold text-xs flex flex-col items-center gap-1.5 transition">
                                    <i class="fa-solid fa-address-card text-base text-emerald-600"></i>
                                    <span>vCard</span>
                                </button>
                                <button type="button" @click="qrType = 'whatsapp'; updateQr()" :class="qrType === 'whatsapp' ? 'bg-sky-50 border-sky-600 text-sky-700 shadow-sm' : 'border-slate-200 hover:bg-slate-50 text-slate-700'" class="p-3 rounded-2xl border text-center font-bold text-xs flex flex-col items-center gap-1.5 transition">
                                    <i class="fa-brands fa-whatsapp text-base text-green-600"></i>
                                    <span>WhatsApp</span>
                                </button>
                                <button type="button" @click="qrType = 'text'; updateQr()" :class="qrType === 'text' ? 'bg-sky-50 border-sky-600 text-sky-700 shadow-sm' : 'border-slate-200 hover:bg-slate-50 text-slate-700'" class="p-3 rounded-2xl border text-center font-bold text-xs flex flex-col items-center gap-1.5 transition">
                                    <i class="fa-solid fa-align-left text-base text-purple-600"></i>
                                    <span>Plain Text</span>
                                </button>
                            </div>
                        </div>

                        <!-- Dynamic Inputs -->
                        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200 space-y-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">2. Enter QR Content</label>
                            
                            <!-- Type 1: URL -->
                            <div x-show="qrType === 'url'" class="space-y-2">
                                <label class="block text-xs font-semibold text-slate-700">Website or Destination Link</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fa-solid fa-link"></i></span>
                                    <input type="url" x-model="urlData" @input="updateQr()" placeholder="https://example.com/my-page" class="w-full pl-10 pr-4 py-3 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none transition"/>
                                </div>
                                <p class="text-[11px] text-slate-500">Enter any web page, portfolio, Google Maps link, or YouTube video.</p>
                            </div>

                            <!-- Type 2: Bio Profile -->
                            <div x-show="qrType === 'profile'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Your Name</label>
                                        <input type="text" x-model="profileData.name" @input="updateQr()" placeholder="e.g. John Doe" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Headline / Role</label>
                                        <input type="text" x-model="profileData.role" @input="updateQr()" placeholder="e.g. Founder & Tech Lead" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Website or Portfolio</label>
                                        <input type="url" x-model="profileData.website" @input="updateQr()" placeholder="https://abctechnologies.com" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Phone / WhatsApp</label>
                                        <input type="text" x-model="profileData.phone" @input="updateQr()" placeholder="+1 999 999 9999" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Type 3: vCard -->
                            <div x-show="qrType === 'vcard'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Full Name</label>
                                        <input type="text" x-model="vcardData.name" @input="updateQr()" placeholder="Sarah Sharma" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Phone Number</label>
                                        <input type="tel" x-model="vcardData.phone" @input="updateQr()" placeholder="+1 555 123 4567" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Email Address</label>
                                        <input type="email" x-model="vcardData.email" @input="updateQr()" placeholder="sarah@example.com" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Company / Organization</label>
                                        <input type="text" x-model="vcardData.company" @input="updateQr()" placeholder="Tech Studio" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Type 4: WhatsApp -->
                            <div x-show="qrType === 'whatsapp'" class="space-y-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">WhatsApp Phone Number (with Country Code)</label>
                                    <input type="text" x-model="whatsappData.phone" @input="updateQr()" placeholder="19999999999" class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Prefilled Message (Optional)</label>
                                    <input type="text" x-model="whatsappData.message" @input="updateQr()" placeholder="Hi! I got your contact via QR code." class="w-full px-3.5 py-2.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"/>
                                </div>
                            </div>

                            <!-- Type 5: Plain Text -->
                            <div x-show="qrType === 'text'" class="space-y-2">
                                <label class="block text-xs font-semibold text-slate-700">Any Message, Note or WiFi Details</label>
                                <textarea x-model="textData" @input="updateQr()" rows="3" placeholder="Enter any text you want to encode..." class="w-full p-3.5 bg-white rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-sky-500 outline-none"></textarea>
                            </div>
                        </div>

                        <!-- 3. Styling & Colors -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">3. Customize Design & Colors</label>
                            <div class="flex flex-wrap items-center gap-4 bg-slate-50/70 p-4 rounded-2xl border border-slate-200">
                                <div>
                                    <span class="block text-[11px] font-bold text-slate-600 mb-1.5">Foreground Color</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="fgColor = '#0f172a'; updateQr()" class="w-7 h-7 rounded-full bg-slate-900 border-2" :class="fgColor === '#0f172a' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-transparent'"></button>
                                        <button type="button" @click="fgColor = '#0284c7'; updateQr()" class="w-7 h-7 rounded-full bg-sky-600 border-2" :class="fgColor === '#0284c7' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-transparent'"></button>
                                        <button type="button" @click="fgColor = '#4f46e5'; updateQr()" class="w-7 h-7 rounded-full bg-indigo-600 border-2" :class="fgColor === '#4f46e5' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-transparent'"></button>
                                        <button type="button" @click="fgColor = '#059669'; updateQr()" class="w-7 h-7 rounded-full bg-emerald-600 border-2" :class="fgColor === '#059669' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-transparent'"></button>
                                        <button type="button" @click="fgColor = '#e11d48'; updateQr()" class="w-7 h-7 rounded-full bg-rose-600 border-2" :class="fgColor === '#e11d48' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-transparent'"></button>
                                        <input type="color" x-model="fgColor" @input="updateQr()" class="w-7 h-7 rounded-lg cursor-pointer border border-slate-300" title="Custom color"/>
                                    </div>
                                </div>

                                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                                <div>
                                    <span class="block text-[11px] font-bold text-slate-600 mb-1.5">Background</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="bgColor = '#ffffff'; updateQr()" class="w-7 h-7 rounded-full bg-white border-2" :class="bgColor === '#ffffff' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-slate-300'"></button>
                                        <button type="button" @click="bgColor = '#f8fafc'; updateQr()" class="w-7 h-7 rounded-full bg-slate-100 border-2" :class="bgColor === '#f8fafc' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-slate-300'"></button>
                                        <button type="button" @click="bgColor = '#fefce8'; updateQr()" class="w-7 h-7 rounded-full bg-amber-50 border-2" :class="bgColor === '#fefce8' ? 'border-sky-500 ring-2 ring-sky-300' : 'border-slate-300'"></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Live QR Preview & Actions -->
                    <div class="lg:col-span-5 flex flex-col items-center">
                        <div class="w-full max-w-sm bg-gradient-to-b from-slate-900 to-slate-950 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col items-center text-center border border-slate-800">
                            <div class="flex items-center justify-between w-full mb-4 px-1">
                                <span class="text-xs font-bold uppercase tracking-wider text-sky-400">Live Preview</span>
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> Scannable
                                </span>
                            </div>

                            <!-- QR Code Container Element -->
                            <div class="p-4 rounded-2xl bg-white shadow-2xl flex items-center justify-center min-w-[220px] min-h-[220px]">
                                <div id="live-qr-canvas" class="flex items-center justify-center"></div>
                            </div>

                            <!-- Encoded Preview Info -->
                            <div class="mt-4 w-full bg-slate-800/80 rounded-xl p-2.5 text-left border border-slate-700/60">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Encoded Data</p>
                                <p class="text-xs text-slate-200 font-mono truncate" x-text="encodedData"></p>
                            </div>

                            <!-- Download Actions -->
                            <div class="mt-5 grid grid-cols-2 gap-2.5 w-full">
                                <button type="button" @click="downloadPng()" class="w-full py-2.5 px-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold text-xs shadow-md shadow-sky-500/20 flex items-center justify-center gap-1.5 transition">
                                    <i class="fa-solid fa-download"></i> Download PNG
                                </button>
                                <button type="button" @click="downloadSvg()" class="w-full py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold text-xs border border-slate-700 flex items-center justify-center gap-1.5 transition">
                                    <i class="fa-solid fa-vector-square"></i> Download SVG
                                </button>
                            </div>
                        </div>

                        <!-- Dynamic Profile Upsell Card -->
                        <div class="mt-6 w-full max-w-sm bg-gradient-to-tr from-sky-50 to-indigo-50 border border-sky-200/80 rounded-2xl p-4 text-left">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-cloud-bolt"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-900">Need a Dynamic Profile?</h4>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                        Update your destination links anytime without reprinting your QR, and track scan analytics.
                                    </p>
                                    <a href="{{ route('register') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-sky-700 hover:text-sky-800 underline">
                                        Create Free Dynamic Account <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- TAB 2: SIGN IN WITH EMAIL & PASSWORD (Option to login directly from first page) -->
            <div x-show="activeTab === 'login'" x-transition.opacity.duration.250ms class="p-6 sm:p-12">
                <div class="max-w-md mx-auto">
                    
                    @auth
                        <!-- Authenticated User Greeting -->
                        <div class="bg-sky-50 border border-sky-200 rounded-2xl p-6 text-center">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white flex items-center justify-center text-2xl font-black mx-auto mb-3 shadow-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <h3 class="text-xl font-black text-slate-900">Welcome Back, {{ Auth::user()->name }}!</h3>
                            <p class="text-xs text-slate-600 mt-1 font-medium">You are currently signed in as <span class="font-bold text-slate-800">{{ Auth::user()->email }}</span></p>

                            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('dashboard.index') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-md transition">
                                    <i class="fa-solid fa-gauge mr-1.5"></i> Open Dashboard
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm rounded-xl transition">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Login Box for User Dashboard -->
                        <div class="text-center mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-md mb-3">
                                <i class="fa-solid fa-gauge-high"></i>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-[10px] font-black uppercase tracking-wider mb-2">User Dashboard Login Box</span>
                            <h2 class="text-2xl font-black text-sky-600">Login Box &mdash; Open User Dashboard</h2>
                            <p class="text-xs text-slate-500 mt-1">Provide your registered Email ID and Password to unlock and open your user dashboard.</p>
                        </div>

                        <!-- Security & Auth Notice -->
                        <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 text-xs p-3.5 rounded-2xl font-semibold flex items-start gap-2.5 shadow-sm">
                            <i class="fa-solid fa-shield-halved text-amber-600 text-base mt-0.5 shrink-0"></i>
                            <div>
                                <span class="font-black block uppercase text-[10px] tracking-wider text-amber-700">Protected User Area</span>
                                <span>Without your Email ID and Password, the user page cannot be opened. Enter your credentials below to proceed.</span>
                            </div>
                        </div>

                        <!-- Status & Validation Alerts -->
                        @if(session('status'))
                            <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-3.5 rounded-xl font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 text-xs p-3.5 rounded-xl font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" @submit="submittingLogin = true" class="space-y-4">
                            @csrf
                            
                            <!-- Email ID Input -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    <i class="fa-regular fa-envelope text-sky-600 mr-1"></i> Email ID
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                                        <i class="fa-solid fa-at"></i>
                                    </span>
                                    <input type="email" 
                                           id="home_email_field"
                                           name="email" 
                                           x-model="loginEmail"
                                           value="{{ old('email') }}" 
                                           required 
                                           placeholder="john@example.com" 
                                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition font-medium"/>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    <i class="fa-solid fa-key text-sky-600 mr-1"></i> Password
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           id="home_password_field"
                                           name="password" 
                                           x-model="loginPassword"
                                           required 
                                           placeholder="••••••••" 
                                           class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-sm outline-none transition font-medium"/>
                                    <button type="button" 
                                            @click="showPassword = !showPassword" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm p-1 focus:outline-none" 
                                            aria-label="Toggle Password Visibility">
                                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember & Forgot Password -->
                            <div class="flex items-center justify-between text-xs font-semibold pt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remember" class="rounded text-sky-600 focus:ring-sky-500"/>
                                    <span class="text-slate-600">Keep me logged in</span>
                                </label>
                                <a href="{{ route('password.request') }}" class="text-sky-600 hover:underline">Forgot Password?</a>
                            </div>

                            <!-- Submit Button to Open Dashboard -->
                            <button type="submit" 
                                    :disabled="submittingLogin" 
                                    class="w-full py-3.5 px-4 font-black text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 rounded-xl shadow-lg shadow-sky-500/25 transition text-sm flex items-center justify-center gap-2 mt-2">
                                <span x-show="!submittingLogin" class="flex items-center gap-2">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Verify & Open Dashboard
                                </span>
                                <span x-show="submittingLogin" class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...
                                </span>
                            </button>
                        </form>

                        <!-- Quick Fill Demo Accounts (4 Cards) -->
                        <div class="mt-6 pt-5 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">⚡ 1-Click Test Credentials:</span>
                                <span class="text-[10px] font-bold text-slate-400">Pass: password</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" 
                                        @click="fillCredentials('john@example.com', 'password')"
                                        class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 text-left transition group">
                                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-sky-600 truncate">John Doe (CEO)</div>
                                    <div class="text-[10px] text-slate-400 font-mono">john@example.com</div>
                                </button>
                                <button type="button" 
                                        @click="fillCredentials('sarah@example.com', 'password')"
                                        class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 text-left transition group">
                                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-sky-600 truncate">Sarah (Creator)</div>
                                    <div class="text-[10px] text-slate-400 font-mono">sarah@example.com</div>
                                </button>
                                <button type="button" 
                                        @click="fillCredentials('alex@example.com', 'password')"
                                        class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 text-left transition group">
                                    <div class="text-[11px] font-bold text-slate-800 group-hover:text-sky-600 truncate">Alex (Dev)</div>
                                    <div class="text-[10px] text-slate-400 font-mono">alex@example.com</div>
                                </button>
                                <button type="button" 
                                        @click="fillCredentials('admin@qrsocialsaas.com', 'password')"
                                        class="p-2 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-200 text-left transition group">
                                    <div class="text-[11px] font-bold text-indigo-700 truncate">System Admin</div>
                                    <div class="text-[10px] text-slate-400 font-mono">admin@...</div>
                                </button>
                            </div>
                        </div>

                        <!-- Registration Prompt -->
                        <p class="text-center text-xs text-slate-600 mt-6">
                            Don't have an account yet? 
                            <a href="{{ route('register') }}" class="font-bold text-sky-600 hover:underline">Create Free Account</a>
                        </p>
                    @endauth

                </div>
            </div>

        </div>
    </section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-20 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900">How It Works</h2>
        <p class="mt-3 text-slate-600">Get your dynamic profile active in under 2 minutes.</p>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">1</div>
                <h3 class="font-bold text-slate-900 mb-2">Create Profile</h3>
                <p class="text-sm text-slate-600">Enter your contact details, bio, and custom profile links.</p>
            </div>
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">2</div>
                <h3 class="font-bold text-slate-900 mb-2">Generate QR</h3>
                <p class="text-sm text-slate-600">System generates a dynamic vector QR code pointing to your slug.</p>
            </div>
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">3</div>
                <h3 class="font-bold text-slate-900 mb-2">Download & Print</h3>
                <p class="text-sm text-slate-600">Export high-resolution PNG, SVG, or print-ready PDF frames.</p>
            </div>
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center">
                <div class="w-12 h-12 bg-sky-600 text-white rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-4">4</div>
                <h3 class="font-bold text-slate-900 mb-2">Track Scans</h3>
                <p class="text-sm text-slate-600">Get real-time scan metrics, link clicks, device, and location analytics.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900">Simple, Transparent Pricing</h2>
        <p class="mt-3 text-slate-600">Start free and scale as your network grows.</p>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
            <!-- Free Plan -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Starter Free</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">$0 <span class="text-sm font-normal text-slate-500">/ forever</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> 1 Dynamic QR Profile</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Standard Social Links</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Basic QR Customization</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> PNG Download</li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3 px-4 font-bold text-sky-600 border border-sky-600 rounded-xl hover:bg-sky-50 transition">Get Started</a>
            </div>

            <!-- Pro Plan -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl border border-slate-800 shadow-xl flex flex-col justify-between relative">
                <span class="absolute -top-3.5 right-8 bg-sky-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">POPULAR</span>
                <div>
                    <h3 class="text-xl font-bold">Pro Creator</h3>
                    <p class="text-3xl font-extrabold mt-4">$9 <span class="text-sm font-normal text-slate-400">/ month</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-300">
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Unlimited Profiles</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Remove Branding</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Vector SVG & PDF Frames</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> Advanced Real-time Analytics</li>
                        <li><i class="fa-solid fa-check text-sky-400 mr-2"></i> VCF Contact Save Button</li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3 px-4 font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-500 shadow-lg transition">Start Free Trial</a>
            </div>

            <!-- Business Plan -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Business & Teams</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">$29 <span class="text-sm font-normal text-slate-500">/ month</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Everything in Pro</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Bulk CSV QR Generation</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> REST API Access</li>
                        <li><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Outbound Webhooks</li>
                    </ul>
                </div>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center py-3 px-4 font-bold text-slate-900 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Contact Sales</a>
            </div>
        </div>
    </div>
</section>

</div>

<!-- Alpine Logic for Instant QR & In-Page Login -->
<script>
function homeExperience() {
    return {
        activeTab: @json($errors->any() || session('status') ? 'login' : 'qr'),
        qrType: 'url',
        urlData: 'https://qrsocialsaas.com',
        profileData: {
            name: 'John Doe',
            role: 'CEO & Founder',
            website: 'https://abctechnologies.com',
            phone: '+1 999 999 9999'
        },
        vcardData: {
            name: 'Sarah Sharma',
            phone: '+1 555 123 4567',
            email: 'sarah@example.com',
            company: 'Tech Studio'
        },
        whatsappData: {
            phone: '19999999999',
            message: 'Hello! I scanned your QR code.'
        },
        textData: 'Welcome to our store! Scan to connect with us.',
        fgColor: '#0f172a',
        bgColor: '#ffffff',
        encodedData: '',
        qrInstance: null,

        // Login Form States
        loginEmail: @json(old('email', '')),
        loginPassword: '',
        showPassword: false,
        submittingLogin: false,

        init() {
            // Check URL hash for direct login tab opening
            if (window.location.hash === '#login' || window.location.hash === '#login-card') {
                this.activeTab = 'login';
            }

            window.addEventListener('switch-to-login', () => {
                this.setTab('login');
                const target = document.getElementById('interactive-section');
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            });

            this.$nextTick(() => {
                this.updateQr();
            });
        },

        setTab(tab) {
            this.activeTab = tab;
            if (tab === 'qr') {
                this.$nextTick(() => {
                    this.updateQr();
                });
            }
        },

        computePayload() {
            if (this.qrType === 'url') {
                return (this.urlData || '').trim() || 'https://qrsocialsaas.com';
            }
            if (this.qrType === 'profile') {
                const parts = [];
                if (this.profileData.name) parts.push(this.profileData.name);
                if (this.profileData.role) parts.push(this.profileData.role);
                if (this.profileData.website) parts.push(this.profileData.website);
                if (this.profileData.phone) parts.push('Tel: ' + this.profileData.phone);
                return parts.length ? parts.join(' | ') : 'https://qrsocialsaas.com';
            }
            if (this.qrType === 'vcard') {
                const fn = (this.vcardData.name || 'Contact').trim();
                const tel = (this.vcardData.phone || '').trim();
                const email = (this.vcardData.email || '').trim();
                const org = (this.vcardData.company || '').trim();
                return `BEGIN:VCARD\nVERSION:3.0\nFN:${fn}\nTEL:${tel}\nEMAIL:${email}\nORG:${org}\nEND:VCARD`;
            }
            if (this.qrType === 'whatsapp') {
                const cleanPhone = (this.whatsappData.phone || '').replace(/[^0-9]/g, '');
                const encodedMsg = encodeURIComponent(this.whatsappData.message || '');
                return `https://wa.me/${cleanPhone}?text=${encodedMsg}`;
            }
            if (this.qrType === 'text') {
                return (this.textData || '').trim() || 'Hello from QR Identity!';
            }
            return 'https://qrsocialsaas.com';
        },

        updateQr() {
            const payload = this.computePayload();
            this.encodedData = payload;

            const container = document.getElementById('live-qr-canvas');
            if (!container) return;

            // Clear previous QR element
            container.innerHTML = '';

            if (typeof QRCode !== 'undefined') {
                try {
                    this.qrInstance = new QRCode(container, {
                        text: payload,
                        width: 200,
                        height: 200,
                        colorDark: this.fgColor,
                        colorLight: this.bgColor,
                        correctLevel: QRCode.CorrectLevel.H
                    });
                } catch (e) {
                    console.error('Error rendering QRCode:', e);
                }
            } else {
                container.innerHTML = '<p class="text-xs text-slate-400">Loading QR library...</p>';
            }
        },

        downloadPng() {
            const container = document.getElementById('live-qr-canvas');
            if (!container) return;

            const canvas = container.querySelector('canvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = 'my-qr-code.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            } else {
                const img = container.querySelector('img');
                if (img && img.src) {
                    const link = document.createElement('a');
                    link.download = 'my-qr-code.png';
                    link.href = img.src;
                    link.click();
                }
            }
        },

        downloadSvg() {
            const payload = encodeURIComponent(this.encodedData);
            const fg = encodeURIComponent(this.fgColor);
            const bg = encodeURIComponent(this.bgColor);
            window.location.href = `{{ route('qr.download.instant') }}?format=svg&data=${payload}&fg=${fg}&bg=${bg}`;
        },

        fillCredentials(email, pwd) {
            this.loginEmail = email;
            this.loginPassword = pwd;
        }
    };
}
</script>
@endsection
