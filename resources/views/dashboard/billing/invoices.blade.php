@extends('layouts.dashboard')

@section('title', 'Invoices & Billing History - QR Identity')

@section('content')
<div class="space-y-8" x-data="{ showModal: false, activePayment: null }">
    <!-- Header & Sub-Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Invoice History</h1>
            <p class="text-xs text-slate-500 mt-1">Search, filter, view details, and download official billing invoices.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl text-xs font-bold">
            <a href="{{ route('dashboard.billing.index') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Overview</a>
            <a href="{{ route('dashboard.billing.invoices') }}" class="px-4 py-2 rounded-xl bg-white text-slate-900 shadow-sm">Invoices</a>
            <a href="{{ route('dashboard.billing.payment-methods') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900">Payment Methods</a>
        </div>
    </div>

    <!-- Filters, Search & Sorting Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
        <form action="{{ route('dashboard.billing.invoices') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <!-- Search Field -->
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by transaction ID or invoice #" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs outline-none focus:ring-2 focus:ring-sky-500"/>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs outline-none font-semibold text-slate-700">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Paid / Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <!-- Sorting -->
            <div class="sm:col-span-3">
                <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs outline-none font-semibold text-slate-700">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Sort: Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Sort: Oldest First</option>
                    <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Sort: Highest Amount</option>
                    <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Sort: Lowest Amount</option>
                </select>
            </div>

            <div class="sm:col-span-1 flex items-center">
                <button type="submit" class="w-full py-2.5 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-slate-800 transition">Filter</button>
            </div>
        </form>
    </div>

    <!-- Invoice Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100 bg-slate-50/50">
                        <th class="py-3.5 px-6">Invoice #</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Billing Period</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Payment Method</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">INV-2026-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-4 text-slate-600">{{ $p->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-slate-600 text-xs">{{ $p->created_at->format('M 01') }} - {{ $p->created_at->endOfMonth()->format('M d, Y') }}</td>
                            <td class="py-4 px-4 font-black text-slate-900">${{ number_format($p->amount, 2) }} {{ strtoupper($p->currency) }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 uppercase">{{ $p->status }}</span>
                            </td>
                            <td class="py-4 px-4 text-slate-600 text-xs capitalize"><i class="fa-solid fa-credit-card mr-1 text-slate-400"></i> {{ $p->provider }}</td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button type="button" @click="activePayment = { id: 'INV-2026-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}', date: '{{ $p->created_at->format('F d, Y') }}', amount: '${{ number_format($p->amount, 2) }}', status: '{{ ucfirst($p->status) }}', provider: '{{ ucfirst($p->provider) }}', tx: '{{ $p->transaction_id }}' }; showModal = true" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-eye mr-1"></i> Details
                                </button>
                                <a href="{{ route('dashboard.billing.invoices.download', $p->id) }}" class="px-3 py-1.5 bg-sky-600 text-white text-xs font-bold rounded-lg hover:bg-sky-700 transition">
                                    <i class="fa-solid fa-download mr-1"></i> PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 text-xs">
                                <i class="fa-solid fa-file-invoice text-3xl text-slate-300 block mb-2"></i>
                                No invoices found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Invoice Detail Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak x-transition>
        <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200" @click.outside="showModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Invoice</span>
                    <h3 class="text-xl font-black text-slate-900" x-text="activePayment ? activePayment.id : ''"></h3>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg p-1">&times;</button>
            </div>

            <div class="space-y-4 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Customer Name:</span>
                    <span class="font-bold text-slate-900">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Email:</span>
                    <span class="font-bold text-slate-900">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Invoice Date:</span>
                    <span class="font-bold text-slate-900" x-text="activePayment ? activePayment.date : ''"></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Transaction ID:</span>
                    <span class="font-mono font-bold text-slate-800" x-text="activePayment ? activePayment.tx : ''"></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Payment Status:</span>
                    <span class="font-bold text-emerald-600" x-text="activePayment ? activePayment.status : ''"></span>
                </div>
                <div class="flex justify-between py-3 border-t border-slate-200 text-sm">
                    <span class="font-black text-slate-900">Total Paid:</span>
                    <span class="font-black text-sky-600" x-text="activePayment ? activePayment.amount : ''"></span>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
