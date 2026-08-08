@extends('layouts.app')

@section('title', 'Parent Guardian Portal')
@section('header', 'Parent Guardian Portal')
@section('subheader', 'Child Academic Progress, Real-Time Attendance Alerts & Online Fees')

@section('content')
<div class="space-y-8">
    
    <!-- Multi-Child Switcher Banner -->
    <div class="glass-card p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Enrolled Child</p>
            <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5 font-display">{{ $selectedChild->user->name }}</h3>
            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">
                {{ $selectedChild->schoolClass->name }} - {{ $selectedChild->section->name }} | Roll #: {{ $selectedChild->roll_number }}
            </p>
        </div>

        @if($children->count() > 1)
            <form method="GET" action="{{ route('parent.dashboard') }}" class="flex items-center space-x-3">
                <label class="text-xs font-bold uppercase text-slate-500">Switch Child:</label>
                <select name="child_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ $selectedChild->id == $child->id ? 'selected' : '' }}>
                            {{ $child->user->name }} ({{ $child->schoolClass->name }})
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <!-- Child KPI Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Attendance Percentage</p>
            <h3 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1 font-display">{{ $attendancePercentage }}%</h3>
            <p class="text-xs font-semibold text-emerald-600 mt-2">
                Live Daily Verified
            </p>
        </div>

        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Unpaid Fee Balance</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">${{ number_format($unpaidBalance, 2) }}</h3>
            <p class="text-xs font-semibold text-indigo-600 mt-2">
                <a href="{{ route('parent.fees.index') }}" class="hover:underline">Pay Online &rarr;</a>
            </p>
        </div>

        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Recent Marks Evaluated</p>
            <h3 class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1 font-display">{{ $recentMarks->count() }} Subjects</h3>
            <p class="text-xs font-semibold text-purple-500 mt-2">
                <a href="{{ route('parent.report-card') }}" class="hover:underline">View Report Card &rarr;</a>
            </p>
        </div>
    </div>

    <!-- Quick Navigation Launchpad -->
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('parent.attendance') }}" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Attendance History
        </a>
        <a href="{{ route('parent.report-card') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Term Report Card & GPA
        </a>
        <a href="{{ route('parent.timetable') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Weekly Class Schedule
        </a>
        <a href="{{ route('parent.fees.index') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Pay Tuition Online
        </a>
    </div>

    <!-- Recent Attendance & Notices Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Attendance History (7 Cols) -->
        <div class="lg:col-span-7 flat-table-wrapper">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Attendance Logs</h3>
                    <p class="text-xs text-slate-500">{{ $selectedChild->user->name }}'s daily presence</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($attendances as $att)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $att->date->format('l, M d, Y') }}</p>
                            <p class="text-[11px] text-slate-400">{{ $att->remarks ?? 'On time' }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-700' : ($att->status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                            {{ $att->status }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No attendance entries recorded yet.</div>
                @endforelse
            </div>
        </div>

        <!-- School Notices (5 Cols) -->
        <div class="lg:col-span-5 glass-card p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Parent Notices</h3>
            <div class="space-y-4">
                @forelse($notices as $n)
                    <div class="p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-indigo-600">{{ $n->created_at->format('M d, Y') }}</span>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $n->title }}</h4>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ Str::limit($n->content, 120) }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No active notices.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
