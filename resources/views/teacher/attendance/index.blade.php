@extends('layouts.app')

@section('title', 'Daily Attendance Matrix')
@section('header', 'Daily Attendance Matrix')
@section('subheader', 'Fast 1-Click Batch Attendance Recording with Auto-Parent Alerts')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Section & Date -->
    <div class="glass-card p-6">
        <form method="GET" action="{{ route('teacher.attendance.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Class & Section</label>
                    <select name="section_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>
                                {{ $sec->schoolClass->name }} - {{ $sec->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Attendance Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" 
                           class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
            </div>

            <button type="button" onclick="markAllPresent()" class="px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 hover:bg-emerald-100 text-xs font-bold transition">
                ✓ Mark All Present (1-Click)
            </button>
        </form>
    </div>

    <!-- Attendance Form Matrix -->
    @if(count($students) > 0)
        <form method="POST" action="{{ route('teacher.attendance.store') }}">
            @csrf
            <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div class="flat-table-wrapper">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Attendance Ledger: {{ date('l, F j, Y', strtotime($selectedDate)) }}</h3>
                        <p class="text-xs text-slate-500">{{ count($students) }} Students Enrolled in this Section</p>
                    </div>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                        Save Attendance Ledger
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3.5">Roll #</th>
                                <th class="px-5 py-3.5">Student Name</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5">Remarks / Reason</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $st)
                                @php
                                    $currentStatus = $existingAttendances[$st->id]->status ?? 'present';
                                    $currentRemarks = $existingAttendances[$st->id]->remarks ?? '';
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-5 py-3.5 font-mono font-bold text-indigo-600">{{ $st->roll_number }}</td>
                                    <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $st->user->name }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-center space-x-3">
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="radio" name="attendance[{{ $st->id }}][status]" value="present" 
                                                       {{ $currentStatus === 'present' ? 'checked' : '' }} class="status-radio text-emerald-600 focus:ring-emerald-500">
                                                <span class="text-xs font-bold text-emerald-600">Present</span>
                                            </label>
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="radio" name="attendance[{{ $st->id }}][status]" value="late" 
                                                       {{ $currentStatus === 'late' ? 'checked' : '' }} class="status-radio text-amber-600 focus:ring-amber-500">
                                                <span class="text-xs font-bold text-amber-600">Late</span>
                                            </label>
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="radio" name="attendance[{{ $st->id }}][status]" value="absent" 
                                                       {{ $currentStatus === 'absent' ? 'checked' : '' }} class="status-radio text-rose-600 focus:ring-rose-500">
                                                <span class="text-xs font-bold text-rose-600">Absent</span>
                                            </label>
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="radio" name="attendance[{{ $st->id }}][status]" value="excused" 
                                                       {{ $currentStatus === 'excused' ? 'checked' : '' }} class="status-radio text-purple-600 focus:ring-purple-500">
                                                <span class="text-xs font-bold text-purple-600">Excused</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <input type="text" name="attendance[{{ $st->id }}][remarks]" value="{{ $currentRemarks }}" placeholder="Optional memo..." 
                                               class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                        Save Attendance Ledger
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="glass-card p-10 text-center text-slate-400 text-xs">
            Please select an allocated section to mark attendance.
        </div>
    @endif

</div>

@push('scripts')
<script>
    function markAllPresent() {
        document.querySelectorAll('input[type="radio"][value="present"]').forEach(el => el.checked = true);
    }
</script>
@endpush
@endsection
