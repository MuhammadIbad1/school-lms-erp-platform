@extends('layouts.app')

@section('title', 'Attendance Ledger')
@section('header', 'Attendance History & Log')
@section('subheader', 'Daily Class Presence and Aggregate Attendance Percentage')

@section('content')
<div class="space-y-6">
    
    <!-- Attendance Metric Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Aggregate Percentage</p>
            <h3 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1 font-display">{{ $percentage }}%</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Classes Present / Late</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-display">{{ $presentCount }} Days</h3>
        </div>
        <div class="glass-card p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Instructional Days</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1 font-display">{{ $totalCount }} Days</h3>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daily Attendance Ledger</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Faculty Marker</th>
                        <th class="px-5 py-4">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">{{ $att->date->format('l, M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-700' : ($att->status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $att->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">{{ $att->marker->name }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $att->remarks ?? 'On time' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">No attendance entries recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $attendances->links() }}
        </div>
    </div>

</div>
@endsection
