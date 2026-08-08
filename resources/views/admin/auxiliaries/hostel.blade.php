@extends('layouts.app')

@section('title', 'Hostel & Dormitories')
@section('header', 'Hostel & Dormitory Residences')
@section('subheader', 'Manage Dormitory Buildings, Rooms, and Boarding Allocations')

@section('content')
<div class="space-y-8" x-data="{ hostelModal: false, roomModal: false }">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
            {{ $hostels->count() }} Hostel Residence Buildings
        </span>
        <div class="flex items-center space-x-3">
            <button @click="roomModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Create Dorm Room
            </button>
            <button @click="hostelModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Register Hostel Building
            </button>
        </div>
    </div>

    <!-- Hostels Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($hostels as $h)
            <div class="glass-card p-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-4 mb-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300">{{ $h->type }} Hostel</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $h->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $h->address ?? 'On-campus boarding facility' }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase">Dorm Rooms ({{ $h->rooms->count() }})</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($h->rooms as $room)
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">Room {{ $room->room_number }}</p>
                                    <span class="text-xs font-mono font-bold text-emerald-600">${{ $room->cost_per_bed }}/mo</span>
                                </div>
                                <p class="text-[11px] text-slate-400">Bed Capacity: {{ $room->capacity }}</p>
                                <div class="mt-2 pt-2 border-t border-slate-200/40 dark:border-slate-700/40 text-[11px]">
                                    <span class="font-semibold text-slate-600 dark:text-slate-300">Boarders: {{ $room->students->count() }} enrolled</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ================= MODAL: ADD HOSTEL ================= -->
    <div x-show="hostelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="hostelModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Register Hostel Building</h3>
            <form method="POST" action="{{ route('admin.hostel.hostels.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Building Name</label>
                    <input type="text" name="name" required placeholder="Curie Hall of Sciences" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Hostel Type</label>
                    <select name="type" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        <option value="boys">Boys Hostel</option>
                        <option value="girls">Girls Hostel</option>
                        <option value="mixed">Co-ed Residence</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Address / Campus Wing</label>
                    <input type="text" name="address" placeholder="East Campus Wing 2" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="hostelModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Register Hostel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: ADD ROOM ================= -->
    <div x-show="roomModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="roomModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Dorm Room</h3>
            <form method="POST" action="{{ route('admin.hostel.rooms.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Hostel Building</label>
                    <select name="hostel_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($hostels as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Room Number / ID</label>
                    <input type="text" name="room_number" required placeholder="B-204" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Capacity (Beds)</label>
                        <input type="number" name="capacity" value="2" min="1" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Cost Per Bed ($)</label>
                        <input type="number" step="0.01" name="cost_per_bed" value="350.00" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="roomModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Add Room</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
