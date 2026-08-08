@extends('layouts.app')

@section('title', 'Teacher Workspace')
@section('header', 'Faculty Workspace & Classroom Hub')
@section('subheader', 'Today\'s Schedule, Daily Attendance Ledger, and Grading Desk')

@section('content')
<div class="space-y-8">
    
    <!-- Quick KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Allocated Students</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">{{ $totalStudents }}</h3>
            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-2">
                Across {{ $allocations->count() }} Subject Allocations
            </p>
        </div>

        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Submissions to Grade</p>
            <h3 class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1 font-display">{{ $pendingSubmissionsCount }}</h3>
            <p class="text-xs font-semibold text-purple-500 mt-2">
                Pending Homework Review
            </p>
        </div>

        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's Class Periods</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-display">{{ $todaySchedule->count() }}</h3>
            <p class="text-xs font-semibold text-emerald-600 mt-2">
                {{ date('l, F j') }}
            </p>
        </div>
    </div>

    <!-- Quick Action Launchpad -->
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('teacher.attendance.index') }}" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Mark Daily Attendance
        </a>
        <a href="{{ route('teacher.gradebook.index') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Enter Exam Marks
        </a>
        <a href="{{ route('teacher.lms.index') }}" class="px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center">
            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Upload Study Materials / Homework
        </a>
    </div>

    <!-- Today's Schedule & Announcements Grid (12-Col) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Today's Classes (7 Cols) -->
        <div class="lg:col-span-7 flat-table-wrapper">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Today's Teaching Schedule</h3>
                    <p class="text-xs text-slate-500">Live lecture timetable for {{ date('l') }}</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($todaySchedule as $period)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                        <div class="flex items-center space-x-4">
                            <span class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center">
                                {{ date('h:i', strtotime($period->timeSlot->start_time)) }}
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $period->subject->name }}</h4>
                                <p class="text-[11px] text-slate-500">{{ $period->section->schoolClass->name }} - {{ $period->section->name }} ({{ $period->room_number ?? 'Main Hall' }})</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ date('h:i A', strtotime($period->timeSlot->start_time)) }} - {{ date('h:i A', strtotime($period->timeSlot->end_time)) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No lecture periods scheduled for today. Enjoy your prep period!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Faculty Bulletins (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Notices -->
            <div class="glass-card p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Faculty Bulletin Board</h3>
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

            <!-- Salary Slips -->
            @if(isset($recentPayrolls) && $recentPayrolls->isNotEmpty())
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">My Salary Payslips</h3>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">Disbursed</span>
                    </div>
                    <div class="space-y-2.5">
                        @foreach($recentPayrolls as $rp)
                            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $rp->month_year }}</p>
                                    <p class="text-[11px] font-mono font-bold text-emerald-600">${{ number_format($rp->net_salary, 2) }} Net</p>
                                </div>
                                <a href="{{ route('teacher.payroll.payslip', $rp) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[11px] shadow-sm transition flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Print / Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
