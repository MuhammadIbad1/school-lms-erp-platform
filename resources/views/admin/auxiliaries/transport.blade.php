@extends('layouts.app')

@section('title', 'Transport & Fleet')
@section('header', 'School Transport & Bus Fleet')
@section('subheader', 'Manage Bus Routes, Transit Vehicles, and Student Route Allocations')

@section('content')
<div class="space-y-8" x-data="{ routeModal: false, vehicleModal: false }">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
            {{ $routes->count() }} Active Bus Routes
        </span>
        <div class="flex items-center space-x-3">
            <button @click="vehicleModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Register Bus Vehicle
            </button>
            <button @click="routeModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Add Transport Route
            </button>
        </div>
    </div>

    <!-- Routes Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($routes as $route)
            <div class="glass-card p-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $route->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $route->description ?? 'Regular morning & afternoon transit route' }}</p>
                    </div>
                    <span class="text-sm font-extrabold text-emerald-600 font-mono">${{ number_format($route->fare, 2) }}/mo</span>
                </div>

                <!-- Assigned Students -->
                <div>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase mb-2">Enrolled Bus Riders ({{ $route->studentTransports->count() }})</p>
                    <div class="space-y-1.5">
                        @forelse($route->studentTransports as $st)
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $st->student->user->name }} ({{ $st->student->schoolClass->name }})</span>
                                <span class="font-mono text-slate-400">Bus: {{ $st->vehicle->vehicle_number }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No students currently assigned to this route.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Vehicles Fleet Flat Table -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Registered Fleet Vehicles</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Vehicle Number</th>
                        <th class="px-5 py-4">Driver Name</th>
                        <th class="px-5 py-4">Driver Contact Phone</th>
                        <th class="px-5 py-4">Passenger Capacity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($vehicles as $veh)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-mono font-bold text-indigo-600">{{ $veh->vehicle_number }}</td>
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">{{ $veh->driver_name }}</td>
                            <td class="px-5 py-4">{{ $veh->driver_phone }}</td>
                            <td class="px-5 py-4 font-bold">{{ $veh->capacity }} Seats</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= MODAL: ADD ROUTE ================= -->
    <div x-show="routeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="routeModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Transport Route</h3>
            <form method="POST" action="{{ route('admin.transport.routes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Route Name</label>
                    <input type="text" name="name" required placeholder="Route #4: Uptown - West Campus" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Monthly Fare ($)</label>
                    <input type="number" step="0.01" name="fare" required placeholder="85.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Route Stops & Description</label>
                    <textarea name="description" placeholder="Pick up at 7:15 AM at Central Station..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="routeModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Add Route</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: ADD VEHICLE ================= -->
    <div x-show="vehicleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="vehicleModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Register Bus Vehicle</h3>
            <form method="POST" action="{{ route('admin.transport.vehicles.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Vehicle Plate / Bus Number</label>
                    <input type="text" name="vehicle_number" required placeholder="BUS-2026-12" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Driver Name</label>
                    <input type="text" name="driver_name" required placeholder="Capt. Edward Davis" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Driver Phone Number</label>
                    <input type="text" name="driver_phone" required placeholder="+1 (555) 019-3388" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Capacity (Passenger Seats)</label>
                    <input type="number" name="capacity" value="35" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="vehicleModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Register Bus</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
