@extends('layouts.app')

@section('title', 'Pay Child Tuition Fees')
@section('header', 'Student Fee Invoices & Payment Portal')
@section('subheader', $child->user->name . ' (' . $child->schoolClass->name . ' - ' . $child->section->name . ')')

@section('content')
<div class="space-y-6" x-data="{ payModal: false, selectedInvoice: null, selectedInvoiceTitle: '', selectedDue: 0, paymentType: 'card' }">
    
    <!-- Balance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Invoiced</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">${{ number_format($totalInvoiced, 2) }}</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Paid</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-display">${{ number_format($totalPaid, 2) }}</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Outstanding Balance Due</p>
            <h3 class="text-3xl font-extrabold text-rose-500 mt-1 font-display">${{ number_format($dueBalance, 2) }}</h3>
        </div>
    </div>

    <!-- Invoices Flat Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Fee Invoices & Checkout Desk</h3>
            <p class="text-xs text-slate-500">Pay directly with card online or request cash verification at the school accounts desk</p>
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
                        <th class="px-5 py-4 text-right">Action / Payment</th>
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
                                @if($inv->status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-700">Paid</span>
                                @elseif($inv->status === 'pending_approval')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 animate-pulse">Pending HR Approval</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-700">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('admin.fees.receipt', $inv) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold transition">
                                    Receipt
                                </a>
                                @if($inv->status === 'unpaid' || $inv->status === 'partially_paid')
                                    <button @click="payModal = true; selectedInvoice = '{{ $inv->id }}'; selectedInvoiceTitle = '{{ $inv->title }}'; selectedDue = {{ $inv->due_balance }}; paymentType = 'card';" 
                                            class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                                        Pay ${{ number_format($inv->due_balance, 2) }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">No invoices generated for this student.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= MODAL: PAYMENT CHECKOUT (CARD OR CASH AT SCHOOL) ================= -->
    <div x-show="payModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="payModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                    💳
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Fee Settlement Checkout</h3>
                    <p class="text-xs text-slate-500" x-text="selectedInvoiceTitle"></p>
                </div>
            </div>

            <!-- Payment Mode Selector Tabs -->
            <div class="grid grid-cols-2 gap-2 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                <button type="button" @click="paymentType = 'card'" 
                        :class="paymentType === 'card' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500'" 
                        class="py-2 rounded-xl text-xs font-bold transition text-center">
                    💳 Online Card
                </button>
                <button type="button" @click="paymentType = 'cash_request'" 
                        :class="paymentType === 'cash_request' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500'" 
                        class="py-2 rounded-xl text-xs font-bold transition text-center">
                    💵 Cash at School Desk
                </button>
            </div>

            <form :action="'/parent/fees/' + selectedInvoice + '/pay'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="payment_type" :value="paymentType">

                <div class="p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                    <p class="text-xs uppercase font-bold text-emerald-800 dark:text-emerald-300">Amount Due to Settle</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-0.5 font-mono" x-text="'$' + selectedDue.toFixed(2)"></p>
                </div>

                <!-- Card Payment Fields -->
                <div x-show="paymentType === 'card'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Cardholder Name</label>
                        <input type="text" name="card_name" value="{{ auth()->user()->name }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Card Number (Visa / Mastercard)</label>
                        <input type="text" name="card_number" value="4242 •••• •••• 9842" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Expiry Date</label>
                            <input type="text" placeholder="MM/YY" value="12/28" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium text-center font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">CVC Code</label>
                            <input type="password" value="882" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium text-center font-mono">
                        </div>
                    </div>
                </div>

                <!-- Cash / Desk Deposit Fields -->
                <div x-show="paymentType === 'cash_request'" class="space-y-3">
                    <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-xs text-amber-800 dark:text-amber-200">
                        <p class="font-bold">🏫 School Cash Desk Instructions:</p>
                        <p class="mt-1 leading-relaxed text-[11px]">Pay the cash amount directly at the school accounts counter. Submit this request and HR will verify and approve your receipt on the spot.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Cash Memo / Bank Reference Note</label>
                        <input type="text" name="cash_memo" placeholder="e.g. Paid in cash at accounts counter on Aug 8" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="payModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition"
                            x-text="paymentType === 'card' ? 'Authorize Instant Payment' : 'Submit for HR Cash Verification'">
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
