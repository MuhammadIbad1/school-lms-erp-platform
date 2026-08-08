@extends('layouts.app')

@section('title', 'Exam Assessment Gradebook')
@section('header', 'Exam Assessment Gradebook')
@section('subheader', 'Score Entry Matrix with Real-Time Percentage & Grade Rule Calculation')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Selectors -->
    <div class="glass-card p-6">
        <form method="GET" action="{{ route('teacher.gradebook.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Select Examination</label>
                <select name="exam_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                    @foreach($exams as $ex)
                        <option value="{{ $ex->id }}" {{ $selectedExamId == $ex->id ? 'selected' : '' }}>{{ $ex->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Subject & Class Section</label>
                <select name="subject_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                    @foreach($allocations as $al)
                        <option value="{{ $al->subject_id }}" {{ $selectedSubjectId == $al->subject_id ? 'selected' : '' }}>
                            {{ $al->subject->schoolClass->name }} - {{ $al->subject->name }} ({{ $al->section->name }})
                        </option>
                    @endforeach
                </select>
                @if($allocations->isNotEmpty())
                    <input type="hidden" name="section_id" value="{{ $selectedSectionId ?? $allocations->first()->section_id }}">
                @endif
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold transition">
                    Refresh Assessment Matrix
                </button>
            </div>
        </form>
    </div>

    <!-- Marks Entry Matrix -->
    @if(count($students) > 0 && $currentSubject)
        <form method="POST" action="{{ route('teacher.gradebook.store') }}">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
            <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
            <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">

            <div class="flat-table-wrapper">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $currentSubject->name }} Marks Matrix</h3>
                        <p class="text-xs text-slate-500">Max Marks: {{ $currentSubject->max_marks }} | Passing Marks: {{ $currentSubject->pass_marks }}</p>
                    </div>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                        Save Assessment Scores
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3.5">Roll #</th>
                                <th class="px-5 py-3.5">Student Name</th>
                                <th class="px-5 py-3.5">Marks Obtained (Max: {{ $currentSubject->max_marks }})</th>
                                <th class="px-5 py-3.5">Teacher Assessment Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $st)
                                @php
                                    $score = $existingMarks[$st->id]->marks_obtained ?? '';
                                    $remarks = $existingMarks[$st->id]->remarks ?? '';
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3.5 font-mono font-bold text-indigo-600">{{ $st->roll_number }}</td>
                                    <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $st->user->name }}</td>
                                    <td class="px-5 py-3.5">
                                        <input type="number" step="0.01" max="{{ $currentSubject->max_marks }}" min="0" 
                                               name="marks[{{ $st->id }}][marks_obtained]" value="{{ $score }}" required
                                               class="w-32 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono font-bold text-indigo-600">
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <input type="text" name="marks[{{ $st->id }}][remarks]" value="{{ $remarks }}" placeholder="e.g. Excellent computational logic" 
                                               class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                        Save Assessment Scores
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
