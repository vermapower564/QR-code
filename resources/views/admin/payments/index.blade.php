@extends('layouts.admin')

@section('title', 'Global Payment Records')

@section('content')
<h2 class="text-xl font-bold text-white mb-6">Payment Transactions</h2>

<div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden">
    <table class="w-full text-left text-xs">
        <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-800">
            <tr>
                <th class="p-4">Transaction ID</th>
                <th class="p-4">Customer</th>
                <th class="p-4">Provider</th>
                <th class="p-4">Amount</th>
                <th class="p-4">Status</th>
                <th class="p-4">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800 text-slate-300">
            @foreach($payments as $pay)
                <tr>
                    <td class="p-4 font-mono font-bold text-white">{{ $pay->transaction_id }}</td>
                    <td class="p-4">{{ $pay->user ? $pay->user->name : 'N/A' }}</td>
                    <td class="p-4 uppercase font-bold text-purple-400">{{ $pay->provider }}</td>
                    <td class="p-4 font-bold text-emerald-400">${{ number_format($pay->amount, 2) }}</td>
                    <td class="p-4"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-emerald-950 text-emerald-300">{{ $pay->status }}</span></td>
                    <td class="p-4 text-slate-400">{{ $pay->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $payments->links() }}
</div>
@endsection
