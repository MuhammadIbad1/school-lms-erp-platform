@extends('layouts.app')

@section('title', 'Academic Hierarchy')
@section('header', 'Academic Hierarchy & Curriculum Engine')
@section('subheader', 'Configure Academic Years, Classes, Sections, and Subject Allocations')

@section('content')
<div class="space-y-8" x-data="{ classModal: false, sectionModal: false, subjectModal: false, yearModal: false }">
    
    <!-- Header Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                {{ $classes->count() }} Classes Configured
            </span>
            <span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs">
                Active Year: {{ $academicYears->firstWhere('is_current', true)?->name ?? '2026-2027' }}
            </span>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="yearModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + New Academic Session
            </button>
            <button @click="classModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Create Class
            </button>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($classes as $class)
            <div class="glass-card p-6 relative">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-4 mb-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">Code: {{ $class->numeric_code }}</span>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $class->name }}</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-500">{{ $class->student_profiles_count }} Enrolled Students</span>
                </div>

                <!-- Sections List -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase">Sections ({{ $class->sections->count() }})</p>
                        <button @click="sectionModal = true" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">+ Add Section</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($class->sections as $sec)
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $sec->name }}</p>
                                    <p class="text-[11px] text-slate-500">Teacher: {{ $sec->classTeacher?->user?->name ?? 'Unassigned' }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300">Cap: {{ $sec->capacity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Subjects List -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase">Subjects ({{ $class->subjects->count() }})</p>
                        <button @click="subjectModal = true" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">+ Add Subject</button>
                    </div>
                    <div class="space-y-1.5">
                        @foreach($class->subjects as $sub)
                            <div class="p-2.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $sub->name }} ({{ $sub->code }})</span>
                                </div>
                                <div class="text-[11px] text-slate-500 space-x-2">
                                    <span class="capitalize">Type: {{ $sub->type }}</span>
                                    <span>Max: {{ $sub->max_marks }}</span>
                                    <span>Pass: {{ $sub->pass_marks }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ================= MODAL: CREATE CLASS ================= -->
    <div x-show="classModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="classModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Create Academic Class</h3>
            <form method="POST" action="{{ route('admin.academics.class.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class Name</label>
                    <input type="text" name="name" required placeholder="e.g. Grade 11" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Numeric Code</label>
                    <input type="number" name="numeric_code" required placeholder="11" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="classModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Create Class</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: CREATE SECTION ================= -->
    <div x-show="sectionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="sectionModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Class Section</h3>
            <form method="POST" action="{{ route('admin.academics.section.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Select Class</label>
                    <select name="class_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Section Name</label>
                    <input type="text" name="name" required placeholder="Section A" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Capacity</label>
                    <input type="number" name="capacity" value="40" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class Teacher In-charge</label>
                    <select name="class_teacher_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        <option value="">-- Optional / None --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->user->name }} ({{ $t->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="sectionModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Add Section</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: CREATE SUBJECT ================= -->
    <div x-show="subjectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="subjectModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Curriculum Subject</h3>
            <form method="POST" action="{{ route('admin.academics.subject.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class</label>
                    <select name="class_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Subject Name</label>
                    <input type="text" name="name" required placeholder="e.g. Organic Chemistry" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Subject Code</label>
                    <input type="text" name="code" required placeholder="CHEM-201" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Max Marks</label>
                        <input type="number" name="max_marks" value="100" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pass Marks</label>
                        <input type="number" name="pass_marks" value="40" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Subject Type</label>
                    <select name="type" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        <option value="theory">Theory Only</option>
                        <option value="practical">Practical / Lab Only</option>
                        <option value="both">Theory + Practical Combined</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="subjectModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Add Subject</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
