@extends('layouts.app')

@section('title', 'LMS Studio & Assignments')
@section('header', 'LMS Studio & Assignments Hub')
@section('subheader', 'Upload Lecture Handouts, Create Homework, and Review Student Work')

@section('content')
<div class="space-y-8" x-data="{ materialModal: false, assignmentModal: false }">
    
    <!-- Header Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
            LMS Content Studio
        </span>
        <div class="flex items-center space-x-3">
            <button @click="materialModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Upload Study Handout
            </button>
            <button @click="assignmentModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Create Homework Assignment
            </button>
        </div>
    </div>

    <!-- Active Assignments Grid -->
    <div class="space-y-4">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Active Homework Assignments</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($assignments as $assign)
                <div class="glass-card p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                {{ $assign->subject->name }} ({{ $assign->section->name }})
                            </span>
                            <span class="text-[11px] font-bold text-rose-500">
                                Due: {{ $assign->due_date->format('M d, H:i') }}
                            </span>
                        </div>
                        <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ $assign->title }}</h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ Str::limit($assign->description, 140) }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Submissions: <strong class="text-slate-800 dark:text-slate-200">{{ $assign->total_submissions }}</strong></span>
                        <a href="{{ route('teacher.lms.assignments.submissions', $assign) }}" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition">
                            Grading Desk &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-8 text-center text-slate-400 glass-card text-xs">
                    No homework assignments published. Click + Create Homework Assignment to post one!
                </div>
            @endforelse
        </div>
    </div>

    <!-- Study Materials Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Published Classroom Study Materials</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Resource Title</th>
                        <th class="px-5 py-4">Subject & Section</th>
                        <th class="px-5 py-4">Description</th>
                        <th class="px-5 py-4 text-right">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($materials as $mat)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white flex items-center">
                                <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-bold flex items-center justify-center mr-2.5 text-[10px] uppercase">
                                    {{ $mat->file_type ?? 'PDF' }}
                                </span>
                                {{ $mat->title }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-indigo-600">{{ $mat->subject->name }} - {{ $mat->section->name }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ Str::limit($mat->description, 60) }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ asset('storage/' . $mat->file_path) }}" target="_blank" class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 font-bold text-xs">
                                    View File
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">No study materials uploaded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= MODAL: ADD STUDY MATERIAL ================= -->
    <div x-show="materialModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="materialModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Upload Study Material</h3>
            <form method="POST" action="{{ route('teacher.lms.materials.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Subject & Section</label>
                    <select name="subject_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($allocations as $al)
                            <option value="{{ $al->subject_id }}">{{ $al->subject->name }} ({{ $al->section->name }})</option>
                        @endforeach
                    </select>
                    @if($allocations->isNotEmpty())
                        <input type="hidden" name="section_id" value="{{ $allocations->first()->section_id }}">
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Resource Title</label>
                    <input type="text" name="title" required placeholder="Differential Calculus Formulas PDF" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Notes / Description</label>
                    <textarea name="description" placeholder="Read Chapter 4 before next lecture..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Upload Document (PDF, DOCX, ZIP)</label>
                    <input type="file" name="file" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="materialModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Publish Resource</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: CREATE ASSIGNMENT ================= -->
    <div x-show="assignmentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="assignmentModal = false" class="glass-card w-full max-w-lg p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Create Homework Assignment</h3>
            <form method="POST" action="{{ route('teacher.lms.assignments.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Subject & Section</label>
                    <select name="subject_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($allocations as $al)
                            <option value="{{ $al->subject_id }}">{{ $al->subject->name }} ({{ $al->section->name }})</option>
                        @endforeach
                    </select>
                    @if($allocations->isNotEmpty())
                        <input type="hidden" name="section_id" value="{{ $allocations->first()->section_id }}">
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Assignment Title</label>
                    <input type="text" name="title" required placeholder="Project: Binary Search Tree Implementation" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Assignment Instructions</label>
                    <textarea name="description" rows="3" required placeholder="Write a complete Python class for BST traversal..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Due Date & Time</label>
                        <input type="datetime-local" name="due_date" required value="{{ date('Y-m-d\TH:i', strtotime('+7 days')) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Max Marks</label>
                        <input type="number" name="max_marks" value="100" min="1" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Optional Specs Attachment</label>
                    <input type="file" name="attachment" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="assignmentModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Post Assignment</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
