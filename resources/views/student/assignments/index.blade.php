@extends('layouts.app')

@section('title', 'Homework & Assignments')
@section('header', 'Homework & Assignment Hub')
@section('subheader', 'Submit Completed Work, Track Deadlines, and View Teacher Feedback')

@section('content')
<div class="space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($assignments as $assign)
            @php
                $sub = $submissions[$assign->id] ?? null;
            @endphp
            <div class="glass-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-[10px] uppercase">
                            {{ $assign->subject->name }}
                        </span>
                        <span class="text-xs font-bold {{ $assign->is_overdue && !$sub ? 'text-rose-500' : 'text-slate-400' }}">
                            Due: {{ $assign->due_date->format('M d, H:i') }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $assign->title }}</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed whitespace-pre-line">{{ $assign->description }}</p>

                    @if($assign->attachment_path)
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $assign->attachment_path) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                Download Assignment Specs
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Submission Status / Upload Form -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    @if($sub)
                        <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">✓ Submitted on {{ $sub->submitted_at->format('M d, H:i') }}</span>
                                @if($sub->marks_obtained)
                                    <span class="text-xs font-extrabold text-emerald-700 dark:text-emerald-300">
                                        Score: {{ $sub->marks_obtained }} / {{ $assign->max_marks }}
                                    </span>
                                @endif
                            </div>
                            @if($sub->feedback)
                                <p class="text-[11px] text-emerald-700 dark:text-emerald-300 mt-1 italic">Feedback: "{{ $sub->feedback }}"</p>
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ route('student.assignments.submit', $assign) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Upload Your Solution File (PDF, ZIP, DOCX)</label>
                                <input type="file" name="submission_file" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            </div>
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                                Turn In Assignment
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 p-10 text-center text-slate-400 glass-card text-xs">
                No active homework assignments due.
            </div>
        @endforelse
    </div>

</div>
@endsection
