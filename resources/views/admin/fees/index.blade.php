@extends('layouts.app')

@section('title', 'Finance & Fees Engine')
@section('header', 'Finance & Student Fee Engine')
@section('subheader', 'Fee Master Rules, 1-Click Batch Invoicing, HR Cash Approvals, and Payment Ledger')

@section('content')
<div class="space-y-8" x-data="{ groupModal: false, masterModal: false, batchModal: false, payModal: false, currentInvoiceId: '' }">
    
    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Invoiced</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">${{ number_format($totalInvoiced, 2) }}</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Collected</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-display">${{ number_format($totalCollected, 2) }}</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Outstanding Due</p>
            <h3 class="text-3xl font-extrabold text-rose-500 mt-1 font-display">${{ number_format($totalPending, 2) }}</h3>
        </div>
    </div>

    <!-- HR Cash / Bank Verification Approval Queue -->
    @if(isset($pendingApprovalInvoices) && $pendingApprovalInvoices->isNotEmpty())
        <div class="glass-card p-6 border-2 border-amber-500/50 bg-amber-50/20 dark:bg-amber-950/20">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-md animate-pulse">
                        ⏳
                    </span>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Cash & Bank Payment Verifications (HR Approval Queue)</h3>
                        <p class="text-xs text-slate-500">Parents have submitted payment requests awaiting physical cash/bank verification at the Accounts Desk.</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-300">
                    {{ $pendingApprovalInvoices->count() }} Pending Verification
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-amber-100/60 dark:bg-amber-950/60 text-slate-700 dark:text-slate-200 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-4 py-3">Student & Class</th>
                            <th class="px-4 py-3">Invoice #</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Amount Due</th>
                            <th class="px-4 py-3 text-right">HR Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-200/60 dark:divide-amber-900/60">
                        @foreach($pendingApprovalInvoices as $pInv)
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-amber-950/30">
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                    {{ $pInv->student->user->name }}
                                    <span class="block text-[11px] font-normal text-slate-500">
                                        {{ $pInv->student->schoolClass->name }} - {{ $pInv->student->section->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-indigo-600">{{ $pInv->invoice_number }}</td>
                                <td class="px-4 py-3">{{ $pInv->title }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 dark:text-white text-sm">${{ number_format($pInv->due_balance, 2) }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <form method="POST" action="{{ route('admin.fees.approve-cash', $pInv) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition inline-flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve & Mark Paid
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.fees.reject-cash', $pInv) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-rose-600 hover:bg-rose-50 text-xs font-bold transition">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Actions Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Fee Masters & Invoicing Engine</h3>
        <div class="flex items-center space-x-3">
            <button @click="groupModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Fee Category
            </button>
            <button @click="masterModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Class Fee Master
            </button>
            <button @click="batchModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                1-Click Batch Invoicing
            </button>
        </div>
    </div>

    <!-- Student Invoices Ledger -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">All Student Invoices Ledger</h3>
                <p class="text-xs text-slate-500">Live invoice statuses, payments, and receipt vouchers</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Invoice #</th>
                        <th class="px-5 py-4">Student & Class</th>
                        <th class="px-5 py-4">Invoice Title</th>
                        <th class="px-5 py-4">Total Amount</th>
                        <th class="px-5 py-4">Paid Amount</th>
                        <th class="px-5 py-4">Due Date</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">{{ $inv->invoice_number }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $inv->student?->user?->name ?? 'Student' }}
                                <span class="block text-[11px] font-normal text-slate-400">
                                    {{ $inv->student?->schoolClass?->name }} - {{ $inv->student?->section?->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4">{{ $inv->title }}</td>
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">${{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-5 py-4 font-bold text-emerald-600">${{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-5 py-4">{{ $inv->due_date->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                @if($inv->status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-700">Paid</span>
                                @elseif($inv->status === 'pending_approval')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800">Pending HR Approval</span>
                                @elseif($inv->status === 'partially_paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-700">Partially Paid</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-700">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('admin.fees.receipt', $inv) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold transition">
                                    Receipt
                                </a>
                                @if($inv->status === 'pending_approval')
                                    <form method="POST" action="{{ route('admin.fees.approve-cash', $inv) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                                            ✓ Approve Cash
                                        </button>
                                    </form>
                                @elseif($inv->status !== 'paid')
                                    <button @click="payModal = true; currentInvoiceId = '{{ $inv->id }}'" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                                        + Record Pay
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- ================= MODAL: FEE GROUP ================= -->
    <div x-show="groupModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="groupModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Create Fee Category</h3>
            <form method="POST" action="{{ route('admin.fees.groups.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Category Name</label>
                    <input type="text" name="name" required placeholder="e.g. Science & Lab Fee" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Description</label>
                    <input type="text" name="description" placeholder="Optional notes" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="groupModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: FEE MASTER ================= -->
    <div x-show="masterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="masterModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Assign Fee Master to Class</h3>
            <form method="POST" action="{{ route('admin.fees.masters.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class</label>
                    <select name="class_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Fee Category</label>
                    <select name="fee_group_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($feeGroups as $fg)
                            <option value="{{ $fg->id }}">{{ $fg->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Amount ($)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="450.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="masterModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Assign Fee Master</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: 1-CLICK BATCH INVOICING ================= -->
    <div x-show="batchModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="batchModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <div class="flex items-center space-x-3 mb-3">
                <span class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">⚡</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">1-Click Batch Invoicing Engine</h3>
            </div>
            <p class="text-xs text-slate-500 mb-4">Generates individualized fee invoices for all students enrolled in the selected class simultaneously.</p>
            <form method="POST" action="{{ route('admin.fees.generate-batch') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Target Class</label>
                    <select name="class_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->studentProfiles->count() }} Students)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Invoice Title</label>
                    <input type="text" name="title" required value="Monthly Tuition & Curriculum Fee" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Payment Due Date</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+15 days')) }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="batchModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Generate Invoices</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: RECORD PAYMENT ================= -->
    <div x-show="payModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="payModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Record Student Payment</h3>
            <form method="POST" action="{{ route('admin.fees.record-payment') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="fee_invoice_id" :value="currentInvoiceId">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Amount Paid ($)</label>
                    <input type="number" step="0.01" name="amount_paid" required placeholder="570.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        <option value="cash">Cash Payment at Accounts Desk</option>
                        <option value="card">Credit / Debit Card Swipe</option>
                        <option value="bank_transfer">Direct Wire / Bank Deposit</option>
                        <option value="online">Online Payment Gateway</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Payment Notes</label>
                    <input type="text" name="notes" placeholder="Receipt #, Cheque ref, or parent memo" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="payModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-md">Record & Generate Receipt</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
