@extends('layouts.app')

@section('title', 'Official Report Card & GPA')
@section('header', 'Academic Performance & Report Card')
@section('subheader', 'Cumulative GPA, Assessment Scores, and Printable Report Card')

@section('content')
<div class="space-y-6">
    
    <!-- Exam Selector & Print Action -->
    <div class="glass-card p-6 flex flex-wrap items-center justify-between gap-4 no-print">
        <form method="GET" action="{{ route('student.report-card') }}" class="flex items-center space-x-3">
            <label class="text-xs font-bold uppercase text-slate-500">Term Exam:</label>
            <select name="exam_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                @foreach($exams as $ex)
                    <option value="{{ $ex->id }}" {{ $selectedExamId == $ex->id ? 'selected' : '' }}>{{ $ex->name }}</option>
                @endforeach
            </select>
        </form>

        <button onclick="window.print()" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Report Card
        </button>
    </div>

    <!-- Official Report Card Sheet -->
    <div class="glass-card p-8 md:p-12 print-full-width border border-slate-200 bg-white">
        
        <!-- School Header -->
        <div class="border-b-2 border-slate-900 pb-6 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 font-display">EduNova Academy & College</h1>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Official Student Term Academic Report Card</p>
                <p class="text-xs text-slate-400">Academic Session 2026-2027</p>
            </div>
            <div class="text-right">
                <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-800">
                    Grade: {{ $finalGrade?->grade_name ?? 'A+' }}
                </span>
                <p class="text-xs font-bold text-slate-700 mt-2">GPA: {{ $finalGrade?->grade_point ?? '4.00' }} / 4.00</p>
            </div>
        </div>

        <!-- Student Meta -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 mb-8 text-xs">
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Student Name</p>
                <p class="font-extrabold text-slate-900 mt-0.5">{{ $student->user->name }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Admission #</p>
                <p class="font-mono font-bold text-indigo-600 mt-0.5">{{ $student->admission_number }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Class & Section</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $student->schoolClass->name }} - {{ $student->section->name }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase text-[10px]">Roll Number</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $student->roll_number }}</p>
            </div>
        </div>

        <!-- Marks Matrix Table -->
        <table class="w-full text-left text-xs mb-8">
            <thead class="border-y-2 border-slate-900 text-slate-700 uppercase text-[10px] font-extrabold">
                <tr>
                    <th class="py-3">Subject Name</th>
                    <th class="py-3">Subject Code</th>
                    <th class="py-3 text-center">Max Marks</th>
                    <th class="py-3 text-center">Passing</th>
                    <th class="py-3 text-center">Marks Obtained</th>
                    <th class="py-3 text-center">Percentage</th>
                    <th class="py-3 text-center">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-slate-800">
                @forelse($marks as $m)
                    <tr>
                        <td class="py-3.5 font-bold text-slate-900">{{ $m->subject->name }}</td>
                        <td class="py-3.5 font-mono text-slate-500">{{ $m->subject->code }}</td>
                        <td class="py-3.5 text-center font-mono">{{ $m->subject->max_marks }}</td>
                        <td class="py-3.5 text-center font-mono text-slate-500">{{ $m->subject->pass_marks }}</td>
                        <td class="py-3.5 text-center font-mono font-extrabold text-indigo-600">{{ $m->marks_obtained }}</td>
                        <td class="py-3.5 text-center font-mono font-bold">{{ $m->percentage }}%</td>
                        <td class="py-3.5 text-center font-extrabold text-emerald-600">{{ $m->grade?->grade_name ?? 'A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No published scores for this term.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t-2 border-slate-900 font-bold">
                <tr>
                    <td colspan="2" class="py-4 font-extrabold text-slate-900">Aggregate Cumulative Result</td>
                    <td class="py-4 text-center font-mono font-extrabold">{{ $totalMax }}</td>
                    <td></td>
                    <td class="py-4 text-center font-mono font-extrabold text-indigo-600 text-sm">{{ $totalObtained }}</td>
                    <td class="py-4 text-center font-mono font-extrabold text-slate-900 text-sm">{{ $overallPercentage }}%</td>
                    <td class="py-4 text-center font-extrabold text-emerald-600 text-base">{{ $finalGrade?->grade_name ?? 'A+' }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Sign-off -->
        <div class="pt-12 grid grid-cols-3 gap-8 text-center text-xs text-slate-500">
            <div class="border-t border-slate-300 pt-2">
                <p class="font-bold text-slate-800">Class Teacher In-charge</p>
                <p class="text-[10px]">Academic Signature</p>
            </div>
            <div class="border-t border-slate-300 pt-2">
                <p class="font-bold text-slate-800">Principal & Dean</p>
                <p class="text-[10px]">Institutional Seal</p>
            </div>
            <div class="border-t border-slate-300 pt-2">
                <p class="font-bold text-slate-800">Parent / Guardian</p>
                <p class="text-[10px]">Acknowledgement</p>
            </div>
        </div>

    </div>

</div>
@endsection
