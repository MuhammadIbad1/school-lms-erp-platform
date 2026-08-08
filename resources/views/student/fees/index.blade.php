@extends('layouts.app')

@section('title', 'My Fee Invoices')
@section('header', 'Student Fee Invoices & Payment Ledger')
@section('subheader', 'Institutional Tuition, Laboratory Fees, and Printable Receipts')

@section('content')
<div class="space-y-6">
    
    <!-- Balance Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Billed</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">${{ number_format($totalInvoiced, 2) }}</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Paid</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-display">${{ number_format($totalPaid, 2) }}</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Outstanding Due</p>
            <h3 class="text-3xl font-extrabold text-rose-500 mt-1 font-display">${{ number_format($dueBalance, 2) }}</h3>
        </div>
    </div>

    <!-- Invoices Flat Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Fee Invoices & Vouchers</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Invoice #</th>
                        <th class="px-5 py-4">Invoice Title</th>
                        <th class="px-5 py-4">Total Amount</th>
                        <th class="px-5 py-4">Paid Amount</th>
                        <th class="px-5 py-4">Due Date</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Receipt Voucher</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">{{ $inv->invoice_number }}</td>
                            <td class="px-5 py-4 font-bold">{{ $inv->title }}</td>
                            <td class="px-5 py-4 font-bold">${{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-5 py-4 font-bold text-emerald-600">${{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-5 py-4">{{ $inv->due_date->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.fees.receipt', $inv) }}" target="_blank" class="px-4 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold transition">
                                    Print Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">No invoices on record.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
