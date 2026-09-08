@extends('layouts.dashboard')

@section('title', 'Payment Methods - QR Identity')

@section('content')
<div class="space-y-8" x-data="{ showAddModal: false, showRemoveModal: false, targetMethodId: null }">
    <!-- Header & Sub-Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Payment Methods</h1>
            <p class="text-xs text-slate-500 mt-1">Manage saved credit cards, default payment options, and billing authorization.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
            <a href="{{ route('dashboard.billing.index') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Overview</a>
            <a href="{{ route('dashboard.billing.invoices') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Invoices</a>
            <a href="{{ route('dashboard.billing.payment-methods') }}" class="px-4 py-2 rounded-xl bg-white text-slate-900 shadow-sm">Payment Methods</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Action Bar -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Saved Cards</h2>
        <button type="button" @click="showAddModal = true" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add Payment Method
        </button>
    </div>

    <!-- Saved Payment Methods Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($paymentMethods as $pm)
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative flex flex-col justify-between">
                @if($pm['is_default'])
                    <span class="absolute top-4 right-4 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold uppercase flex items-center gap-1">
                        <i class="fa-solid fa-check"></i> Default Card
                    </span>
                @endif

                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-9 bg-slate-900 text-white rounded-xl flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-brands fa-cc-{{ strtolower($pm['brand']) == 'visa' ? 'visa' : 'mastercard' }} text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">{{ $pm['brand'] }} •••• {{ $pm['last4'] }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Expires {{ $pm['exp_month'] }}/{{ $pm['exp_year'] }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    @if(!$pm['is_default'])
                        <form action="{{ route('billing.payment-methods.default') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method_id" value="{{ $pm['id'] }}">
                            <button type="submit" class="text-xs font-bold text-sky-600 hover:text-sky-700">Set as Default</button>
                        </form>
                    @else
                        <span class="text-xs text-slate-400 font-semibold">Default for auto-renew</span>
                    @endif

                    <button type="button" @click="targetMethodId = '{{ $pm['id'] }}'; showRemoveModal = true" class="text-xs font-bold text-rose-600 hover:text-rose-700">
                        Remove
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Payment Method Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak x-transition>
        <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200" @click.outside="showAddModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-xl font-black text-slate-900">Add Payment Method</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form action="{{ route('billing.payment-methods.add') }}" method="POST" class="space-y-4 text-xs font-bold">
                @csrf
                <input type="hidden" name="token" value="tok_simulated_stripe">
                <div>
                    <label class="block uppercase tracking-wider text-slate-700 mb-1">Cardholder Name</label>
                    <input type="text" name="card_holder" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 font-normal outline-none focus:ring-2 focus:ring-sky-500"/>
                </div>

                <div>
                    <label class="block uppercase tracking-wider text-slate-700 mb-1">Card Information (Tokenized)</label>
                    <div class="p-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-500 font-mono text-xs flex items-center justify-between">
                        <span>•••• •••• •••• 4242</span>
                        <span class="text-slate-400">MM/YY CVC</span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-normal mt-1">Secured via Stripe tokenized encryption. Raw credentials are never stored.</p>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white font-bold rounded-xl shadow-md">Add Card</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Remove Confirmation Modal -->
    <div x-show="showRemoveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak x-transition>
        <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 text-center" @click.outside="showRemoveModal = false">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 text-xl font-bold flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-trash"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Remove Payment Method?</h3>
            <p class="text-xs text-slate-500 mb-6">Are you sure you want to remove this payment method? This action cannot be undone.</p>

            <form :action="`/dashboard/billing/payment-methods/${targetMethodId}`" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="showRemoveModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 text-white font-bold rounded-xl text-xs shadow-md">Yes, Remove Card</button>
            </form>
        </div>
    </div>
</div>
@endsection
