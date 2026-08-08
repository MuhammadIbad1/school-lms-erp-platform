@extends('layouts.app')

@section('title', 'Submissions: ' . $assignment->title)
@section('header', 'Homework Grading Desk')
@section('subheader', 'Review Student Submissions, Assign Marks, and Deliver Constructive Feedback')

@section('content')
<div class="space-y-6">
    
    <!-- Top Assignment Card -->
    <div class="glass-card p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                    {{ $assignment->subject->name }} - {{ $assignment->section->name }}
                </span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $assignment->title }}</h3>
                <p class="text-xs text-slate-500 mt-1">Due: {{ $assignment->due_date->format('M d, Y H:i') }} | Max Score: {{ $assignment->max_marks }}</p>
            </div>
            <a href="{{ route('teacher.lms.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-200">
                &larr; Back to LMS Studio
            </a>
        </div>
    </div>

    <!-- Student Submissions Flat Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Student Uploads & Score Evaluation</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Student</th>
                        <th class="px-5 py-4">Submitted File</th>
                        <th class="px-5 py-4">Time</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Grading & Feedback</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($assignment->submissions as $sub)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $sub->student->user->name }}
                                <span class="block text-[11px] font-mono text-slate-400">Roll: {{ $sub->student->roll_number }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-100 inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download Work
                                </a>
                            </td>
                            <td class="px-5 py-4">{{ $sub->submitted_at->format('M d, H:i') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $sub->status === 'graded' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $sub->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('teacher.lms.submissions.grade', $sub) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    <input type="number" step="0.01" name="marks_obtained" value="{{ $sub->marks_obtained }}" max="{{ $assignment->max_marks }}" min="0" placeholder="Score" required 
                                           class="w-20 px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono font-bold text-xs">
                                    <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="Feedback note..." 
                                           class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs flex-1 min-w-[140px]">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No student submissions uploaded for this homework yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
