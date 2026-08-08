@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('header', 'Student Learning Hub')
@section('subheader', 'Your Daily Classes, Homework Deadlines, Attendance, and Performance')

@section('content')
<div class="space-y-8">
    
    <!-- Student KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Attendance Percentage</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">{{ $attendancePercentage }}%</h3>
            <p class="text-xs font-semibold text-emerald-600 mt-2">
                {{ $student->schoolClass->name }} - {{ $student->section->name }}
            </p>
        </div>

        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Homework Deadlines</p>
            <h3 class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1 font-display">{{ $assignments->count() }}</h3>
            <p class="text-xs font-semibold text-purple-500 mt-2">
                Pending Submissions
            </p>
        </div>

        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Outstanding Balance Due</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">${{ number_format($unpaidFees, 2) }}</h3>
            <p class="text-xs font-semibold text-indigo-600 mt-2">
                Term Fee Status
            </p>
        </div>
    </div>

    <!-- Today's Timetable & Upcoming Exams Grid (12-Col) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Today's Class Schedule (7 Cols) -->
        <div class="lg:col-span-7 flat-table-wrapper">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Today's Class Timetable ({{ date('l') }})</h3>
                    <p class="text-xs text-slate-500">Live lecture schedule for {{ $student->section->name }}</p>
                </div>
                <a href="{{ route('student.timetable') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Full Week &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($todaySchedule as $period)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                        <div class="flex items-center space-x-3.5">
                            <span class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center">
                                {{ date('h:i', strtotime($period->timeSlot->start_time)) }}
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $period->subject->name }}</h4>
                                <p class="text-[11px] text-slate-500">Faculty: {{ $period->teacher->user->name }} • {{ $period->room_number ?? 'Main Classroom' }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ date('h:i A', strtotime($period->timeSlot->start_time)) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No class lectures scheduled for today.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Exam Dates (5 Cols) -->
        <div class="lg:col-span-5 glass-card p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Upcoming Examinations</h3>
            <div class="space-y-3">
                @forelse($upcomingExams as $examSch)
                    <div class="p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-indigo-600 font-mono">{{ $examSch->exam_date->format('M d, Y') }}</span>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-0.5">{{ $examSch->subject->name }}</h4>
                            <p class="text-[11px] text-slate-400">{{ $examSch->exam->name }} ({{ $examSch->room_number ?? 'Main Exam Hall' }})</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                            {{ date('h:i A', strtotime($examSch->start_time)) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No upcoming exam terms scheduled.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Active Homework Deadlines (Flat Table) -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Active Homework & Assignments</h3>
                <p class="text-xs text-slate-500">Upload your completed homework files directly</p>
            </div>
            <a href="{{ route('student.assignments.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">All Homework &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Subject</th>
                        <th class="px-5 py-4">Assignment Title</th>
                        <th class="px-5 py-4">Deadline</th>
                        <th class="px-5 py-4">Submission Status</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($assignments as $assign)
                        @php
                            $sub = $submissions[$assign->id] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-bold text-indigo-600">{{ $assign->subject->name }}</td>
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">{{ $assign->title }}</td>
                            <td class="px-5 py-4 text-rose-500 font-bold">{{ $assign->due_date->format('M d, H:i') }}</td>
                            <td class="px-5 py-4">
                                @if($sub)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $sub->status === 'graded' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $sub->status }} ({{ $sub->marks_obtained ? $sub->marks_obtained . ' Marks' : 'Submitted' }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-700">
                                        Pending Upload
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('student.assignments.index') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700">
                                    {{ $sub ? 'View Work' : 'Submit Now' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No active homework assignments due.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
