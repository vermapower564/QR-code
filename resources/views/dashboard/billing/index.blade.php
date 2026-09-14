@extends('layouts.dashboard')

@section('title', 'Billing & Plans - QR Identity')

@section('content')
<div class="space-y-8" x-data="{ 
    billingCycle: 'monthly', 
    showDetailsModal: false, 
    selectedInvoice: null,
    showConfirmModal: false,
    selectedPlan: null
}">
    <!-- Header & Sub-Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-pulse"></span>
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-sky-600">Account & Subscription</span>
            </div>
            <h1 class="text-2xl font-black text-sky-600 mt-1">Billing & Plans</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage your active subscription, view live plan usage quotas, compare tiers, and download invoices.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Subnav Tabs -->
            <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
                <a href="{{ route('dashboard.billing.index') }}" class="px-4 py-2 rounded-xl bg-white text-sky-600 shadow-sm">Overview & Plans</a>
                <a href="{{ route('dashboard.billing.invoices') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Invoices</a>
                <a href="{{ route('dashboard.billing.payment-methods') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Payment Methods</a>
            </div>
            
            <!-- Quick Test Actions Dropdown / Buttons -->
            <div class="flex items-center gap-2">
                <form action="{{ route('dashboard.billing.simulate') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="amount" value="29.00">
                    <input type="hidden" name="plan_name" value="Business & Teams Monthly Renewal">
                    <button type="submit" title="Generate an example payment transaction for testing" class="px-3 py-2 bg-sky-50 text-sky-700 border border-sky-200 rounded-xl text-xs font-bold hover:bg-sky-100 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle text-sky-600"></i> + Add Example Invoice ($29)
                    </button>
                </form>

                <form action="{{ route('dashboard.billing.reset-examples') }}" method="POST" class="inline" onsubmit="return confirm('Reset all billing history back to realistic defaults?');">
                    @csrf
                    <button type="submit" title="Reset sample billing records" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-right"></i> Reset Examples
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Billing Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Current Plan Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-sky-50 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Current Plan</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider">Active</span>
            </div>
            <h3 class="text-xl font-black text-slate-900">{{ $user->plan ? $user->plan->name : 'Business & Teams' }}</h3>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-check text-sky-600"></i>
                Renews on <span class="font-bold text-slate-800">{{ $currentSubscription && $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : now()->addDays(18)->format('M d, Y') }}</span>
            </p>
        </div>

        <!-- Amount & Billing Cycle Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Cycle Rate</span>
            <div class="flex items-baseline gap-1.5">
                <h3 class="text-3xl font-black text-slate-900">${{ number_format($user->plan ? $user->plan->price : 29, 2) }}</h3>
                <span class="text-xs text-slate-500 font-bold">USD / month</span>
            </div>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Status: <span class="font-bold text-slate-800">Auto-Renew Enabled</span>
            </p>
        </div>

        <!-- Default Payment Method Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Payment Method</span>
            <div class="flex items-center gap-3">
                <div class="w-10 h-7 bg-slate-900 text-white rounded-lg flex items-center justify-center font-bold text-xs shadow-sm">
                    <i class="fa-brands fa-cc-visa text-base"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Visa •••• {{ $defaultPaymentMethod['last4'] }}</h4>
                    <span class="text-[11px] text-slate-500">Exp {{ $defaultPaymentMethod['exp_month'] }}/{{ $defaultPaymentMethod['exp_year'] }}</span>
                </div>
            </div>
            <a href="{{ route('dashboard.billing.payment-methods') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 block mt-3">Manage cards &rarr;</a>
        </div>

        <!-- Account Security / Standing -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Account Standing</span>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i> Good Standing (Paid)
            </div>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1">
                <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i> 256-bit Stripe Tokenized Billing
            </p>
        </div>
    </div>

    <!-- 2. Live Plan Usage Quotas & Examples Meter -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Plan Usage & Limits</h2>
                    <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 text-[10px] font-black uppercase">Example Metrics</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Real-time quota monitoring and consumption for your active subscription cycle.</p>
            </div>
            <span class="text-xs text-slate-400 font-semibold">Resets every 30 days</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Metric 1: Profiles -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-id-card text-sky-600"></i> QR Profiles</span>
                    <span class="text-slate-900 font-black">{{ $usageMetrics['profiles']['used'] }} / {{ $usageMetrics['profiles']['limit'] }}</span>
                </div>
                <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden mb-2">
                    <div class="bg-sky-600 h-full rounded-full transition-all duration-500" style="width: {{ $usageMetrics['profiles']['percentage'] }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 font-medium">Active identity micro-sites</p>
            </div>

            <!-- Metric 2: Monthly Scans -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-qrcode text-emerald-600"></i> Monthly Scans</span>
                    <span class="text-slate-900 font-black">{{ number_format($usageMetrics['scans']['used']) }} / {{ number_format($usageMetrics['scans']['limit']) }}</span>
                </div>
                <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden mb-2">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $usageMetrics['scans']['percentage'] }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 font-medium">{{ $usageMetrics['scans']['percentage'] }}% of cycle limit used</p>
            </div>

            <!-- Metric 3: Custom Links -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-link text-indigo-600"></i> Custom Links</span>
                    <span class="text-slate-900 font-black">{{ $usageMetrics['links']['used'] }} / {{ $usageMetrics['links']['limit'] }}</span>
                </div>
                <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden mb-2">
                    <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $usageMetrics['links']['percentage'] }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 font-medium">Social, booking & custom CTA links</p>
            </div>

            <!-- Metric 4: Team Seats -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-users text-amber-600"></i> Team Members</span>
                    <span class="text-slate-900 font-black">{{ $usageMetrics['team_seats']['used'] }} / {{ $usageMetrics['team_seats']['limit'] }} Seats</span>
                </div>
                <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden mb-2">
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $usageMetrics['team_seats']['percentage'] }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 font-medium">3 seats remaining for invite</p>
            </div>
        </div>
    </div>

    <!-- 3. Available Subscription Plans (Interactive Comparison) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="text-center max-w-xl mx-auto mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-sky-700 text-xs font-extrabold mb-3">
                <i class="fa-solid fa-sparkles"></i> Flexible SaaS Plans
            </div>
            <h2 class="text-2xl font-black text-slate-900">Compare Plans & Examples</h2>
            <p class="text-xs text-slate-500 mt-1">Upgrade, downgrade, or test switching your plan with instant feature activation.</p>

            <!-- Billing Cycle Toggle Switch -->
            <div class="inline-flex items-center bg-slate-100 p-1.5 rounded-2xl mt-5 text-xs font-bold">
                <button type="button" @click="billingCycle = 'monthly'" :class="billingCycle === 'monthly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2 rounded-xl transition">
                    Monthly Billing
                </button>
                <button type="button" @click="billingCycle = 'yearly'" :class="billingCycle === 'yearly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                    <span>Annual Billing</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">Save 20%</span>
                </button>
            </div>
        </div>

        <!-- 3 Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                @php
                    $isCurrent = ($user->plan_id == $plan->id) || (!$user->plan_id && $plan->slug === 'free');
                    $isPro = $plan->slug === 'pro';
                    $isBiz = $plan->slug === 'business';
                    $annualPrice = $plan->price * 10; // 2 months free
                @endphp
                <div class="border {{ $isCurrent ? 'border-sky-500 ring-2 ring-sky-500/20 shadow-md' : 'border-slate-200' }} rounded-3xl p-6 sm:p-7 flex flex-col justify-between hover:border-sky-400 transition relative bg-white">
                    @if($isCurrent)
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-sky-600 text-white text-[10px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow">
                            <i class="fa-solid fa-check-circle mr-1"></i> Current Active Plan
                        </div>
                    @elseif($isPro)
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-500 text-white text-[10px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow">
                            <i class="fa-solid fa-star mr-1"></i> Most Popular
                        </div>
                    @endif

                    <div>
                        <!-- Plan Header -->
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-black uppercase tracking-wider {{ $isCurrent ? 'text-sky-600' : 'text-slate-400' }}">
                                {{ $plan->slug }}
                            </span>
                            @if($plan->price > 0)
                                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md" x-show="billingCycle === 'yearly'">
                                    2 Months Free
                                </span>
                            @endif
                        </div>

                        <h3 class="text-xl font-black text-slate-900">{{ $plan->name }}</h3>
                        
                        <!-- Price Display -->
                        <div class="my-4">
                            <div class="flex items-baseline gap-1" x-show="billingCycle === 'monthly'">
                                <span class="text-3xl font-black text-slate-900">${{ number_format($plan->price, 0) }}</span>
                                <span class="text-xs text-slate-500 font-bold">/ month</span>
                            </div>
                            <div class="flex items-baseline gap-1" x-show="billingCycle === 'yearly'" x-cloak>
                                <span class="text-3xl font-black text-slate-900">${{ number_format($annualPrice, 0) }}</span>
                                <span class="text-xs text-slate-500 font-bold">/ year</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                @if($plan->slug === 'free')
                                    Free forever for individuals & basic QR profiles.
                                @elseif($plan->slug === 'pro')
                                    Ideal for creators, freelancers & consulting experts.
                                @else
                                    For teams, agencies, and high-volume corporate QR codes.
                                @endif
                            </p>
                        </div>

                        <!-- Example Use Cases Tag -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 mb-5">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Example Use Case:</span>
                            <p class="text-xs font-semibold text-slate-700">
                                @if($plan->slug === 'free')
                                    Personal contact card, resume bio link, single digital card.
                                @elseif($plan->slug === 'pro')
                                    Full digital business card, social link tree, YouTube & TikTok portfolio.
                                @else
                                    Corporate staff directory, marketing campaigns, bulk client cards.
                                @endif
                            </p>
                        </div>

                        <!-- Feature List -->
                        <ul class="text-xs space-y-3 text-slate-600 mb-6 font-medium">
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span><strong>{{ $plan->profile_limit == -1 ? 'Unlimited' : $plan->profile_limit }}</strong> Dynamic QR Profiles</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span><strong>{{ $plan->link_limit == -1 ? 'Unlimited' : $plan->link_limit }}</strong> Custom Links & Socials</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>Dynamic Vector QR (PNG, SVG, PDF)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>vCard 3.0 One-Tap Contact Save (.vcf)</span>
                            </li>
                            @if($plan->slug === 'pro' || $plan->slug === 'business')
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    <span>Real-Time Geo & Device Analytics</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    <span>Custom Colors, Hex Gradients & Logo</span>
                                </li>
                            @else
                                <li class="flex items-center gap-2.5 text-slate-400 line-through">
                                    <i class="fa-solid fa-circle-xmark text-slate-300"></i>
                                    <span>Geo Analytics & Custom Colors</span>
                                </li>
                            @endif
                            @if($plan->slug === 'business')
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    <span>Custom Domain & White-Labeling</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    <span>5 Team Seats & Bulk CSV Generator</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Action Button -->
                    <form action="{{ route('dashboard.billing.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="provider" value="stripe">
                        @if($isCurrent)
                            <button type="button" disabled class="w-full py-3 px-4 font-extrabold text-xs rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed text-center">
                                Current Active Plan
                            </button>
                        @else
                            <button type="submit" class="w-full py-3 px-4 font-bold text-xs rounded-xl shadow transition {{ $isPro ? 'bg-sky-600 hover:bg-sky-700 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white' }}">
                                Switch to {{ $plan->name }}
                            </button>
                        @endif
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Detailed Feature Matrix (Full Comparison) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8" x-data="{ expanded: false }">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Complete Feature Comparison</h2>
                <p class="text-xs text-slate-500 mt-0.5">Review detailed capabilities and example quotas for each subscription tier.</p>
            </div>
            <button type="button" @click="expanded = !expanded" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                <span x-text="expanded ? 'Collapse Table' : 'Expand All Features'"></span>
                <i class="fa-solid" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <div class="overflow-x-auto" x-show="expanded" x-transition>
            <table class="w-full text-left text-xs border-collapse mt-4">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-extrabold uppercase">
                        <th class="py-3 px-4 text-slate-800 font-bold">Feature / Specification</th>
                        <th class="py-3 px-4 text-center">Starter Free</th>
                        <th class="py-3 px-4 text-center text-sky-600">Pro Creator</th>
                        <th class="py-3 px-4 text-center text-indigo-600">Business & Teams</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Dynamic QR Profiles</td>
                        <td class="py-3.5 px-4 text-center">1 Profile</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">Unlimited</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Social & Custom Links</td>
                        <td class="py-3.5 px-4 text-center">Up to 5 Links</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">Unlimited</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Monthly Scan Allowance</td>
                        <td class="py-3.5 px-4 text-center">500 Scans / mo</td>
                        <td class="py-3.5 px-4 text-center">10,000 Scans / mo</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">Unlimited Scans</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Custom Colors & Theme Presets</td>
                        <td class="py-3.5 px-4 text-center text-slate-300"><i class="fa-solid fa-minus"></i></td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> 12 Themes</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> Full CSS + Logo</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">vCard 3.0 Direct Contact Save</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> Standard</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> Advanced Photo vCard</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> Corporate vCard</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Custom Branded Subdomains</td>
                        <td class="py-3.5 px-4 text-center text-slate-300"><i class="fa-solid fa-minus"></i></td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> Subdomain</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> Full Custom Domain + SSL</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">White-labeling (No Branding)</td>
                        <td class="py-3.5 px-4 text-center text-slate-300"><i class="fa-solid fa-minus"></i></td>
                        <td class="py-3.5 px-4 text-center text-slate-300"><i class="fa-solid fa-minus"></i></td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600"><i class="fa-solid fa-check"></i> 100% White-Label</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Team Collaboration Seats</td>
                        <td class="py-3.5 px-4 text-center">1 User</td>
                        <td class="py-3.5 px-4 text-center">1 User</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">5 Team Members</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-800">Customer Support</td>
                        <td class="py-3.5 px-4 text-center">Community</td>
                        <td class="py-3.5 px-4 text-center font-bold text-sky-600">Priority Email</td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">24/7 Dedicated SLA</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Recent Invoices Section (With Details & Download) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Recent Invoices & Receipts</h2>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">Official Invoices</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Your itemized billing history, payments, and receipt downloads.</p>
            </div>
            <a href="{{ route('dashboard.billing.invoices') }}" class="px-4 py-2 text-xs font-bold text-sky-600 bg-sky-50 rounded-xl hover:bg-sky-100 transition">
                View All Invoices &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100">
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $p)
                        @php
                            $planName = $p->payment_data['plan'] ?? 'Business & Teams Subscription';
                            $cardBrand = $p->payment_data['card_brand'] ?? 'Visa';
                            $cardLast4 = $p->payment_data['card_last4'] ?? '4242';
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 font-mono font-bold text-slate-900">INV-2026-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-4 text-slate-600 text-xs">{{ $p->created_at ? $p->created_at->format('M d, Y') : now()->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-slate-900 text-xs font-semibold">{{ $planName }}</td>
                            <td class="py-4 px-4 font-black text-slate-900">${{ number_format($p->amount, 2) }} {{ strtoupper($p->currency) }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase {{ $p->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    <i class="fa-solid fa-check text-[10px] mr-1"></i> {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-600 text-xs">
                                <span class="inline-flex items-center gap-1.5 font-semibold">
                                    <i class="fa-brands fa-cc-{{ strtolower($cardBrand) === 'mastercard' ? 'mastercard' : 'visa' }} text-slate-700 text-sm"></i>
                                    {{ $cardBrand }} •••• {{ $cardLast4 }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <button type="button" @click="selectedInvoice = { id: 'INV-2026-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}', date: '{{ $p->created_at ? $p->created_at->format('M d, Y') : now()->format('M d, Y') }}', plan: '{{ addslashes($planName) }}', amount: '${{ number_format($p->amount, 2) }}', status: '{{ ucfirst($p->status) }}', tx: '{{ $p->transaction_id }}', card: '{{ $cardBrand }} •••• {{ $cardLast4 }}' }; showDetailsModal = true" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-eye mr-1"></i> View
                                </button>
                                <a href="{{ route('dashboard.billing.invoices.download', $p->id) }}" class="px-3 py-1.5 bg-sky-600 text-white text-xs font-bold rounded-lg hover:bg-sky-700 transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-download"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 text-xs">
                                No billing invoices found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. Billing Activity Timeline -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-6">Recent Billing Activity & Log</h2>

        <div class="space-y-4">
            @foreach($activityLog as $log)
                <div class="flex items-start gap-4 pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">{{ $log['event'] }}</h3>
                            <span class="text-xs text-slate-400">{{ $log['date'] }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $log['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Invoice Detail Modal -->
    <div x-show="showDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak x-transition>
        <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200" @click.outside="showDetailsModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Invoice Preview</span>
                    <h3 class="text-xl font-black text-slate-900" x-text="selectedInvoice ? selectedInvoice.id : ''"></h3>
                </div>
                <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-slate-600 text-lg p-1">&times;</button>
            </div>

            <div class="space-y-4 text-xs" x-show="selectedInvoice">
                <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl">
                    <div>
                        <span class="text-slate-400 block font-semibold">Issue Date</span>
                        <span class="font-bold text-slate-800" x-text="selectedInvoice ? selectedInvoice.date : ''"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Status</span>
                        <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full text-[10px] uppercase" x-text="selectedInvoice ? selectedInvoice.status : ''"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Payment Method</span>
                        <span class="font-bold text-slate-800" x-text="selectedInvoice ? selectedInvoice.card : ''"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Transaction ID</span>
                        <span class="font-mono text-slate-800 font-bold" x-text="selectedInvoice ? selectedInvoice.tx : ''"></span>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl p-4">
                    <span class="text-xs font-extrabold uppercase text-slate-400 block mb-2">Itemized Charges</span>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <div>
                            <span class="font-bold text-slate-900" x-text="selectedInvoice ? selectedInvoice.plan : ''"></span>
                            <span class="text-[11px] text-slate-500 block">Dynamic QR Code engine & unlimited links</span>
                        </div>
                        <span class="font-bold text-slate-900" x-text="selectedInvoice ? selectedInvoice.amount : ''"></span>
                    </div>
                    <div class="flex items-center justify-between pt-3 font-bold text-slate-900">
                        <span>Total Paid</span>
                        <span class="text-base text-sky-600 font-black" x-text="selectedInvoice ? selectedInvoice.amount + ' USD' : ''"></span>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" @click="showDetailsModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
