@extends('layouts.app')

@section('title', 'Faculty HR & Allocations')
@section('header', 'Faculty HR & Subject Allocations')
@section('subheader', 'Manage Faculty Profiles, Qualifications, and Section Teaching Allocations')

@section('content')
<div class="space-y-8" x-data="{ teacherModal: false, assignModal: false }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold text-xs">
            {{ $teachers->total() }} Faculty Members Registered
        </span>
        <div class="flex items-center space-x-3">
            <button @click="assignModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Assign Subject & Section
            </button>
            <button @click="teacherModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Register New Teacher
            </button>
        </div>
    </div>

    <!-- Teachers Table -->
    <div class="flat-table-wrapper">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Faculty Member</th>
                        <th class="px-5 py-4">Employee Code</th>
                        <th class="px-5 py-4">Qualification / Role</th>
                        <th class="px-5 py-4">Basic Salary</th>
                        <th class="px-5 py-4">Assigned Subjects & Sections</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teachers as $t)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 text-white font-bold flex items-center justify-center mr-3 text-xs">
                                    {{ substr($t->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p>{{ $t->user->name }}</p>
                                    <p class="text-[11px] font-normal text-slate-400">{{ $t->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono font-bold text-purple-600 dark:text-purple-400">{{ $t->employee_code }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $t->designation }}</p>
                                <p class="text-[11px] text-slate-400">{{ $t->qualification }}</p>
                            </td>
                            <td class="px-5 py-4 font-mono font-bold text-emerald-600">${{ number_format($t->basic_salary, 2) }}/mo</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($t->teacherSubjects as $ts)
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-semibold text-[11px] border border-indigo-100 dark:border-indigo-900">
                                            <span>{{ $ts->subject->name }} ({{ $ts->section->name }})</span>
                                            <form method="POST" action="{{ route('admin.teachers.remove-subject', $ts) }}" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                                            </form>
                                        </div>
                                    @empty
                                        <span class="text-slate-400 text-[11px]">No subjects assigned yet</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-400">No faculty members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $teachers->links() }}
        </div>
    </div>

    <!-- ================= MODAL: CREATE TEACHER ================= -->
    <div x-show="teacherModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="teacherModal = false" class="glass-card w-full max-w-lg p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Register Faculty Teacher</h3>
            <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Full Name</label>
                        <input type="text" name="name" required placeholder="Prof. Jane Smith" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email (Login ID)</label>
                        <input type="email" name="email" required placeholder="jane@school.com" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Password</label>
                        <input type="password" name="password" required value="password123" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Employee Code</label>
                        <input type="text" name="employee_code" value="EMP-2026-{{ rand(100, 999) }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Qualification</label>
                        <input type="text" name="qualification" required placeholder="M.Sc. in Physics" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Designation</label>
                        <input type="text" name="designation" value="Senior Faculty" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Joining Date</label>
                        <input type="date" name="joining_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Basic Monthly Salary ($)</label>
                        <input type="number" step="0.01" name="basic_salary" value="5500.00" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="teacherModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Register Teacher</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: ASSIGN SUBJECT ================= -->
    <div x-show="assignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="assignModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Assign Subject & Section</h3>
            <form method="POST" action="{{ route('admin.teachers.assign-subject') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Teacher</label>
                    <select name="teacher_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->user->name }} ({{ $t->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Curriculum Subject</label>
                    <select name="subject_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($classes as $c)
                            @foreach($c->subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $c->name }} - {{ $sub->name }} ({{ $sub->code }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Class Section</label>
                    <select name="section_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($classes as $c)
                            @foreach($c->sections as $sec)
                                <option value="{{ $sec->id }}">{{ $c->name }} - {{ $sec->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="assignModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Assign Allocation</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
