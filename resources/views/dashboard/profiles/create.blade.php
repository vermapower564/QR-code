@extends('layouts.dashboard')

@section('title', 'Create QR Profile')

@section('content')
<div class="max-w-6xl mx-auto"
     x-data="{ 
         step: 1,
         totalSteps: 3,
         submitting: false,
         name: '{{ old('name') }}',
         username: '{{ old('username') }}',
         bio: '{{ old('bio') }}',
         phone: '{{ old('phone') }}',
         email: '{{ old('email') }}',
         website: '{{ old('website') }}',
         designation: '{{ old('designation') }}',
         company: '{{ old('company') }}',
         bioMax: 500,
         avatarPreview: null,
         logoPreview: null,
         
         handleAvatar(e) {
             if(e.target.files.length > 0) {
                 this.avatarPreview = URL.createObjectURL(e.target.files[0]);
             }
         },
         handleLogo(e) {
             if(e.target.files.length > 0) {
                 this.logoPreview = URL.createObjectURL(e.target.files[0]);
             }
         },
         canGoNext() {
             if (this.step === 1) return this.name.trim() !== '' && this.username.trim() !== '';
             if (this.step === 2 && this.phone && this.phone.length !== 10) return false;
             return true;
         },
         nextStep() {
             if (this.canGoNext() && this.step < this.totalSteps) {
                 this.step++;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             }
         },
         prevStep() {
             if (this.step > 1) {
                 this.step--;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             }
         }
     }">

    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Create Dynamic Profile</h2>
            <p class="text-sm text-slate-500 mt-1">Design your digital identity and generate a dynamic QR code.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-sky-700 bg-sky-100 px-4 py-2 rounded-full shadow-sm">
                Step <span x-text="step"></span> of <span x-text="totalSteps"></span>
            </span>
        </div>
    </div>

    <!-- Main Grid: Form Left, Sticky Preview Right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Form Area (Col Span 7) -->
        <div class="lg:col-span-7">
            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <!-- Progress Header -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-slate-100">
                    <div class="h-full bg-gradient-to-r from-sky-400 to-indigo-500 transition-all duration-500 ease-out" :style="'width: ' + (step / totalSteps * 100) + '%'"></div>
                </div>

                @if($errors->any())
                    <div class="mt-4 mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-sm p-4 rounded-xl flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dashboard.profiles.store') }}" method="POST" enctype="multipart/form-data" @submit="submitting = true" class="space-y-8 mt-4">
                    @csrf

                    <!-- STEP 1: Basic Information -->
                    <div x-show="step === 1" x-transition.opacity.duration.300ms>
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-sm"><i class="fa-solid fa-user"></i></span>
                                Basic Details
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 ml-10">This information will be displayed prominently on your profile.</p>
                        </div>

                        <div class="space-y-6 ml-10">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Full Name <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-regular fa-id-badge"></i></div>
                                    <input type="text" name="name" x-model="name" placeholder="John Doe" required class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none"/>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Username / Slug <span class="text-rose-500">*</span></label>
                                <div class="flex rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 overflow-hidden transition-all shadow-sm">
                                    <span class="px-4 py-3.5 text-slate-400 text-sm font-mono border-r border-slate-200 bg-slate-100 flex items-center">domain.com/p/</span>
                                    <input type="text" name="username" x-model="username" placeholder="john-doe" required class="w-full px-4 py-3.5 bg-transparent text-sm font-semibold text-slate-900 outline-none border-none"/>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Designation</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-briefcase"></i></div>
                                        <input type="text" name="designation" x-model="designation" placeholder="e.g. Founder" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none"/>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Company</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-building"></i></div>
                                        <input type="text" name="company" x-model="company" placeholder="e.g. Acme Corp" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none"/>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider">Bio / Description</label>
                                    <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full"><span x-text="bio.length"></span>/<span x-text="bioMax"></span></span>
                                </div>
                                <textarea name="bio" x-model="bio" maxlength="500" rows="3" placeholder="Tell the world a little about yourself..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none leading-relaxed"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Contact & Media -->
                    <div x-show="step === 2" x-transition.opacity.duration.300ms style="display: none;">
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm"><i class="fa-solid fa-address-book"></i></span>
                                Contact & Media
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 ml-10">Upload visuals and add your public contact channels.</p>
                        </div>

                        <div class="space-y-8 ml-10">
                            <!-- Image Uploads -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Avatar Upload -->
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 hover:bg-slate-50 hover:border-sky-300 transition-colors text-center relative group">
                                    <input type="file" name="profile_image" @change="handleAvatar" accept="image/png,image/jpeg,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"/>
                                    <template x-if="!avatarPreview">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center mb-3">
                                                <i class="fa-solid fa-camera text-xl"></i>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700">Upload Avatar</span>
                                            <span class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-widest">PNG, JPG (Max 2MB)</span>
                                        </div>
                                    </template>
                                    <template x-if="avatarPreview">
                                        <div class="flex flex-col items-center">
                                            <img :src="avatarPreview" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg mb-2" />
                                            <span class="text-xs font-bold text-sky-600">Change Avatar</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Logo Upload -->
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 hover:bg-slate-50 hover:border-indigo-300 transition-colors text-center relative group">
                                    <input type="file" name="logo" @change="handleLogo" accept="image/png,image/jpeg,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"/>
                                    <template x-if="!logoPreview">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-3">
                                                <i class="fa-solid fa-building text-xl"></i>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700">Upload Company Logo</span>
                                            <span class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-widest">Optional</span>
                                        </div>
                                    </template>
                                    <template x-if="logoPreview">
                                        <div class="flex flex-col items-center">
                                            <img :src="logoPreview" class="h-16 object-contain mb-2" />
                                            <span class="text-xs font-bold text-indigo-600">Change Logo</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Phone Number</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-phone"></i></div>
                                        <input type="text" name="phone" x-model="phone" @input="phone = phone.replace(/[^0-9]/g, '').slice(0, 10)" maxlength="10" placeholder="9876543210" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none" :class="{'border-rose-400 focus:ring-rose-500 focus:border-rose-500': phone && phone.length !== 10}"/>
                                    </div>
                                    <p x-show="phone && phone.length !== 10" class="text-[11px] text-rose-600 font-bold mt-1.5"><i class="fa-solid fa-circle-exclamation"></i> 10 digits required</p>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Contact Email</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-envelope"></i></div>
                                        <input type="email" name="email" x-model="email" placeholder="hello@example.com" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none"/>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">WhatsApp Number</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-brands fa-whatsapp"></i></div>
                                        <input type="text" name="whatsapp" x-model="whatsapp" placeholder="Include country code (e.g., 1234567890)" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-semibold text-slate-900 transition-all outline-none"/>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1 font-semibold">Optional. If empty, standard phone number will be used if provided.</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Website URL</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-globe"></i></div>
                                        <input type="url" name="website" x-model="website" placeholder="https://yourwebsite.com" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-semibold text-slate-900 transition-all outline-none"/>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Business/Office Address (Get Directions)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 pt-3.5 pointer-events-none text-slate-400"><i class="fa-solid fa-location-dot"></i></div>
                                        <textarea name="address" x-model="address" placeholder="123 Example Street, City, Country" rows="2" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm font-semibold text-slate-900 transition-all outline-none resize-none"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Submit -->
                    <div x-show="step === 3" x-transition.opacity.duration.300ms style="display: none;">
                        <div class="text-center py-10">
                            <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-check text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-2">Ready to Go!</h3>
                            <p class="text-slate-500 text-sm max-w-sm mx-auto mb-8">Review your mock preview on the right. Once created, you can customize the theme colors and add unlimited social links.</p>
                            
                            <button type="submit" :disabled="submitting" class="px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl shadow-xl shadow-slate-900/20 disabled:opacity-50 transition-all transform hover:-translate-y-1">
                                <span x-show="!submitting"><i class="fa-solid fa-wand-magic-sparkles mr-2 text-sky-400"></i> Generate My Profile</span>
                                <span x-show="submitting"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Generating Magic...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Controls -->
                    <div class="pt-8 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" @click="prevStep()" :class="step === 1 ? 'invisible' : ''" :disabled="submitting" class="px-5 py-2.5 font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition text-sm flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left text-xs"></i> Back
                        </button>

                        <button type="button" x-show="step < totalSteps" @click="nextStep()" :disabled="!canGoNext() || submitting" class="px-8 py-3 font-bold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-xl shadow-md shadow-sky-600/20 transition-all text-sm flex items-center gap-2">
                            Continue <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Phone Preview Area (Col Span 5) -->
        <div class="lg:col-span-5 hidden lg:block">
            <div class="sticky top-10 flex justify-center">
                <!-- Phone Mockup Frame -->
                <div class="w-[320px] h-[650px] bg-slate-900 rounded-[3rem] p-3 shadow-2xl relative border-8 border-slate-800 flex flex-col overflow-hidden">
                    <!-- Notch -->
                    <div class="absolute top-0 inset-x-0 h-6 bg-slate-800 w-40 mx-auto rounded-b-3xl z-20"></div>
                    
                    <!-- Screen Content -->
                    <div class="bg-slate-50 flex-1 rounded-[2.25rem] overflow-hidden relative shadow-inner flex flex-col relative z-10">
                        <!-- Profile Header -->
                        <div class="pt-16 pb-6 px-6 bg-white border-b border-slate-100 flex flex-col items-center text-center relative z-20">
                            
                            <!-- Avatar Preview -->
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg z-10 relative"/>
                            </template>
                            <template x-if="!avatarPreview">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-sky-400 to-indigo-500 text-white font-black text-4xl flex items-center justify-center shadow-lg border-4 border-white z-10 relative">
                                    <span x-text="name ? name.charAt(0).toUpperCase() : '?'"></span>
                                </div>
                            </template>
                            
                            <h2 class="text-xl font-black text-slate-900 mt-4 leading-tight" x-text="name || 'Your Name'"></h2>
                            <p class="text-xs font-bold text-sky-600 mt-1" x-show="designation || company">
                                <span x-text="designation"></span>
                                <span x-show="designation && company"> @ </span>
                                <span x-text="company"></span>
                            </p>
                            
                            <p class="text-[11px] text-slate-500 mt-3 max-w-[200px] leading-relaxed break-words" x-text="bio || 'Your bio will appear here...'"></p>
                        </div>
                        
                        <!-- Links Preview -->
                        <div class="flex-1 bg-slate-50 p-5 space-y-3 z-10 overflow-y-auto">
                            <!-- Mock buttons based on input -->
                            <template x-if="phone">
                                <div class="w-full bg-white border border-slate-200 p-3 rounded-2xl flex items-center justify-center gap-2 shadow-sm text-slate-700 text-xs font-bold">
                                    <i class="fa-solid fa-phone text-emerald-500"></i> Call Me
                                </div>
                            </template>
                            <template x-if="email">
                                <div class="w-full bg-white border border-slate-200 p-3 rounded-2xl flex items-center justify-center gap-2 shadow-sm text-slate-700 text-xs font-bold">
                                    <i class="fa-solid fa-envelope text-sky-500"></i> Email Me
                                </div>
                            </template>
                            <template x-if="website">
                                <div class="w-full bg-white border border-slate-200 p-3 rounded-2xl flex items-center justify-center gap-2 shadow-sm text-slate-700 text-xs font-bold">
                                    <i class="fa-solid fa-globe text-indigo-500"></i> Website
                                </div>
                            </template>
                            
                            <!-- Static mockup buttons for aesthetics -->
                            <div class="opacity-40 space-y-3 pt-2">
                                <div class="w-full bg-slate-200 h-10 rounded-2xl"></div>
                                <div class="w-full bg-slate-200 h-10 rounded-2xl"></div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="absolute bottom-0 w-full py-4 text-center bg-gradient-to-t from-slate-50 to-transparent z-20 pointer-events-none">
                            <span class="text-[10px] font-black tracking-widest text-slate-300 uppercase">QR Identity</span>
                        </div>
                    </div>
                </div>
                
                <!-- Floating URL tag -->
                <div class="absolute -right-6 top-20 bg-white shadow-xl shadow-sky-900/10 border border-slate-100 rounded-2xl p-3 transform rotate-3 flex flex-col gap-1 z-30 hidden xl:flex">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Your Link</span>
                    <span class="text-xs font-bold text-sky-600 bg-sky-50 px-2 py-1 rounded-lg">/p/<span x-text="username || '...' "></span></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
