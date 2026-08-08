@extends('layouts.app')

@section('title', 'Study Materials & Handouts')
@section('header', 'Classroom Study Resources')
@section('subheader', 'Lecture Notes, Formula Sheets, and Reading Materials')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($materials as $mat)
            <div class="glass-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-[10px] uppercase">
                            {{ $mat->subject->name }}
                        </span>
                        <span class="text-[10px] uppercase font-bold text-slate-400">{{ $mat->created_at->format('M d, Y') }}</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $mat->title }}</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $mat->description ?? 'No additional notes provided by faculty.' }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">By: <strong class="text-slate-700 dark:text-slate-300">{{ $mat->teacher->user->name }}</strong></span>
                    <a href="{{ asset('storage/' . $mat->file_path) }}" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md">
                        Download Handout
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-10 text-center text-slate-400 glass-card text-xs">
                No study materials or lecture notes uploaded yet. Check back soon!
            </div>
        @endforelse
    </div>
</div>
@endsection
