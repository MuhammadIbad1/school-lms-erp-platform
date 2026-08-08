@extends('layouts.app')

@section('title', 'Student Registry')
@section('header', 'Student Enrollment & Registry')
@section('subheader', 'Student Roster, Academic Placements, and Profile Status')

@section('content')
<div class="space-y-6">
    
    <!-- Top Filter Bar -->
    <div class="glass-card p-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, admission #, roll..." 
                   class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium focus:ring-2 focus:ring-indigo-500 w-full sm:w-64">
            
            <select name="class_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                <option value="">-- All Classes --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold transition">
                Filter
            </button>
        </form>

        <a href="{{ route('admin.students.create') }}" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            + Admit New Student
        </a>
    </div>

    <!-- Students Flat Table -->
    <div class="flat-table-wrapper">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Student Name</th>
                        <th class="px-5 py-4">Admission #</th>
                        <th class="px-5 py-4">Class & Section</th>
                        <th class="px-5 py-4">Parent Guardian</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($students as $st)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center mr-3 text-xs shadow-sm">
                                    {{ substr($st->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p>{{ $st->user->name }}</p>
                                    <p class="text-[11px] font-normal text-slate-400">{{ $st->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $st->admission_number }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-[11px]">
                                    {{ $st->schoolClass->name }} - {{ $st->section->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $st->parent?->user?->name ?? 'None Linked' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $st->parent?->user?->phone ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                @if($st->user->status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">Active</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300">Suspended</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('admin.students.show', $st) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 text-xs font-bold transition">
                                    360° Profile
                                </a>
                                <form method="POST" action="{{ route('admin.students.toggle-status', $st) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $st->user->status === 'active' ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
                                        {{ $st->user->status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">No student profiles match your search criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $students->links() }}
        </div>
    </div>

</div>
@endsection
