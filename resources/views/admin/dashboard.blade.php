@extends('layouts.app')

@section('title', 'Admin Executive Dashboard')
@section('header', 'Executive Command Center')
@section('subheader', 'Institutional Operations & Real-Time Performance Analytics')

@section('content')
<div class="space-y-8">
    
    <!-- 4 KPI Glass Cards (12-Col Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Students -->
        <div class="glass-card p-6 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Enrolled</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">{{ number_format($totalStudents) }}</h3>
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-2 flex items-center">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        100% Active Profiles
                    </p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 p-3.5 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Faculty -->
        <div class="glass-card p-6 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Faculty Staff</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">{{ number_format($totalTeachers) }}</h3>
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-2 flex items-center">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span>
                        {{ $totalClasses }} Academic Classes
                    </p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 p-3.5 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="glass-card p-6 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Fees Collected</p>
                    <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-display">${{ number_format($totalRevenue, 2) }}</h3>
                    <p class="text-xs font-semibold text-rose-500 mt-2 flex items-center">
                        Pending: ${{ number_format($pendingFees, 2) }}
                    </p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 p-3.5 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Today Attendance -->
        <div class="glass-card p-6 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Today's Attendance</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">{{ $todayAttendanceRate }}%</h3>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-2">
                        Institutional Daily Avg
                    </p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 p-3.5 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Action Launchpad -->
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.students.create') }}" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            + Admit Student
        </a>
        <a href="{{ route('admin.academics.index') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Academic Classes
        </a>
        <a href="{{ route('admin.fees.index') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            Batch Invoices
        </a>
        <a href="{{ route('admin.notices.index') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            Broadcast Notice
        </a>
    </div>

    <!-- Charts & Analytics (12-Col Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Attendance & Fee Trend Chart (8 Cols) -->
        <div class="lg:col-span-8 glass-card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Fee Revenue vs Collection Trends</h3>
                    <p class="text-xs text-slate-500">Monthly fiscal cashflow comparisons</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">Current Session</span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Attendance Breakdown Doughnut (4 Cols) -->
        <div class="lg:col-span-4 glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Attendance Ratio</h3>
                <span class="text-xs font-semibold text-emerald-600">Daily Matrix</span>
            </div>
            <div class="h-52 flex items-center justify-center">
                <canvas id="attendanceDoughnut"></canvas>
            </div>
            <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Present</p>
                    <p class="text-xs font-extrabold text-emerald-600">92%</p>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Late</p>
                    <p class="text-xs font-extrabold text-amber-500">5%</p>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Absent</p>
                    <p class="text-xs font-extrabold text-rose-500">3%</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Data Tables: Recent Admissions & Latest Transactions (12-Col Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Recent Admissions (7 Cols) -->
        <div class="lg:col-span-7 flat-table-wrapper">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Enrolled Students</h3>
                    <p class="text-xs text-slate-500">Latest students admitted to academic session</p>
                </div>
                <a href="{{ route('admin.students.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">View Roster &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Student</th>
                            <th class="px-5 py-3.5">Admission #</th>
                            <th class="px-5 py-3.5">Class & Section</th>
                            <th class="px-5 py-3.5">Parent / Guardian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentStudents as $st)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white flex items-center">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-2.5 text-[11px]">
                                        {{ substr($st->user->name, 0, 1) }}
                                    </div>
                                    {{ $st->user->name }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-500">{{ $st->admission_number }}</td>
                                <td class="px-5 py-3.5">{{ $st->schoolClass->name }} - {{ $st->section->name }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $st->parent?->user?->name ?? 'Self/Guardian' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400">No recent students admitted.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Fee Transactions (5 Cols) -->
        <div class="lg:col-span-5 flat-table-wrapper">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Fee Receipts</h3>
                    <p class="text-xs text-slate-500">Live payment verification logs</p>
                </div>
                <a href="{{ route('admin.fees.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">All Invoices &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Student</th>
                            <th class="px-5 py-3.5">Amount</th>
                            <th class="px-5 py-3.5">Method</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentPayments as $pmt)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $pmt->invoice?->student?->user?->name ?? 'Student' }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-emerald-600 dark:text-emerald-400">
                                    +${{ number_format($pmt->amount_paid, 2) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $pmt->payment_method }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-6 text-center text-slate-400">No payment receipts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Revenue Chart
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                    label: 'Collected ($)',
                    data: [12500, 18400, 24000, 31200, 28900, 35400, 42500],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: 'rgba(156, 163, 175, 0.1)' } }
                }
            }
        });

        // Attendance Doughnut
        const ctxAtt = document.getElementById('attendanceDoughnut').getContext('2d');
        new Chart(ctxAtt, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Absent'],
                datasets: [{
                    data: [92, 5, 3],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endpush
@endsection
