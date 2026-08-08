@extends('layouts.app')

@section('title', $child->user->name . ' - Weekly Timetable')
@section('header', 'Weekly Class Schedule')
@section('subheader', $child->user->name . ' (' . $child->schoolClass->name . ' - ' . $child->section->name . ')')

@section('content')
<div class="space-y-6">
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Class Schedule: {{ $child->schoolClass->name }} - {{ $child->section->name }}</h3>
            <button onclick="window.print()" class="no-print px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200">
                Print Timetable
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Time Period</th>
                        @foreach($days as $day)
                            <th class="px-5 py-4 text-center">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($timeSlots as $slot)
                        <tr class="{{ $slot->is_break ? 'bg-amber-50/40 dark:bg-amber-950/20' : '' }}">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}</span>
                                @if($slot->is_break)
                                    <span class="block text-[10px] font-bold uppercase text-amber-600">Break / Recess</span>
                                @endif
                            </td>
                            @foreach($days as $day)
                                <td class="px-4 py-3 text-center">
                                    @if($slot->is_break)
                                        <span class="text-slate-400 text-[11px] font-bold">Recess Interval</span>
                                    @elseif(isset($scheduleGrid[$day][$slot->id]))
                                        @php $item = $scheduleGrid[$day][$slot->id]; @endphp
                                        <div class="p-2.5 rounded-xl bg-indigo-50/80 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-900">
                                            <p class="font-bold text-indigo-700 dark:text-indigo-300 text-xs">{{ $item->subject->name }}</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $item->teacher->user->name }}</p>
                                            <span class="inline-block mt-1 text-[9px] font-mono font-bold text-slate-400">{{ $item->room_number ?? 'Room 101' }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-700">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
