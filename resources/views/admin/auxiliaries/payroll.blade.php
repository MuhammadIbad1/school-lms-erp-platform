@extends('layouts.app')

@section('title', 'Faculty Payroll')
@section('header', 'Faculty Payroll & Salary Disbursement')
@section('subheader', 'Monthly Salary Slip Generation, Allowances, Deductions & Bank Records')

@section('content')
<div class="space-y-8" x-data="{ payrollModal: false }">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs">
            Faculty Monthly Compensation
        </span>
        <button @click="payrollModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
            + Generate Faculty Salary Voucher
        </button>
    </div>

    <!-- Payroll History Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Disbursed Salary History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Faculty Member</th>
                        <th class="px-5 py-4">Month / Cycle</th>
                        <th class="px-5 py-4">Basic Pay</th>
                        <th class="px-5 py-4">Allowances</th>
                        <th class="px-5 py-4">Deductions</th>
                        <th class="px-5 py-4">Net Salary Paid</th>
                        <th class="px-5 py-4">Disbursement Date</th>
                        <th class="px-5 py-4 text-right">Payslip Voucher</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($payrolls as $p)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white flex items-center">
                                <div class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center mr-2.5 text-[11px]">
                                    {{ substr($p->teacher->user->name, 0, 1) }}
                                </div>
                                {{ $p->teacher->user->name }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-indigo-600">{{ $p->month_year }}</td>
                            <td class="px-5 py-4 font-mono">${{ number_format($p->basic_salary, 2) }}</td>
                            <td class="px-5 py-4 font-mono text-emerald-600">+${{ number_format($p->allowances, 2) }}</td>
                            <td class="px-5 py-4 font-mono text-rose-500">-${{ number_format($p->deductions, 2) }}</td>
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white text-sm">${{ number_format($p->net_salary, 2) }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $p->paid_at ? $p->paid_at->format('M d, Y') : 'Pending' }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.payroll.payslip', $p) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-xs font-bold transition inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Print / Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">No payroll vouchers generated.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $payrolls->links() }}
        </div>
    </div>

    <!-- ================= MODAL: GENERATE SALARY ================= -->
    <div x-show="payrollModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="payrollModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Generate Salary Slip</h3>
            <form method="POST" action="{{ route('admin.payroll.generate') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Teacher</label>
                    <select name="teacher_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->user->name }} (Base: ${{ number_format($t->basic_salary, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Payroll Cycle / Month</label>
                    <input type="text" name="month_year" value="{{ date('F Y') }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Allowances ($)</label>
                        <input type="number" step="0.01" name="allowances" value="400.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Deductions ($)</label>
                        <input type="number" step="0.01" name="deductions" value="150.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Disbursement Method</label>
                    <input type="text" name="payment_method" value="Direct Bank Wire Deposit" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="payrollModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-md">Generate & Disburse</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
