@extends('layouts.app')

@section('title', 'Inventory & Asset Management')
@section('header', 'Inventory & School Assets')
@section('subheader', 'Lab Equipment, Robotics Kits, Stationery & Asset Stock Tracking')

@section('content')
<div class="space-y-8" x-data="{ catModal: false, itemModal: false }">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
            {{ $items->total() }} Logged Equipment & Supplies
        </span>
        <div class="flex items-center space-x-3">
            <button @click="catModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + New Asset Category
            </button>
            <button @click="itemModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Log Inventory Item
            </button>
        </div>
    </div>

    <!-- Inventory Flat Table -->
    <div class="flat-table-wrapper">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Item Name / Asset</th>
                        <th class="px-5 py-4">Category</th>
                        <th class="px-5 py-4">In-Stock Quantity</th>
                        <th class="px-5 py-4">Unit Price</th>
                        <th class="px-5 py-4">Total Asset Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">{{ $item->item_name }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-[11px]">
                                    {{ $item->category->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-mono font-bold">{{ $item->quantity }} Units</td>
                            <td class="px-5 py-4 font-mono">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-5 py-4 font-mono font-bold text-emerald-600">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No inventory items recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $items->links() }}
        </div>
    </div>

    <!-- ================= MODAL: ADD CATEGORY ================= -->
    <div x-show="catModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="catModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Inventory Category</h3>
            <form method="POST" action="{{ route('admin.inventory.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Category Name</label>
                    <input type="text" name="name" required placeholder="Robotics & Microcontrollers" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="catModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: ADD ITEM ================= -->
    <div x-show="itemModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="itemModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Log Inventory Asset</h3>
            <form method="POST" action="{{ route('admin.inventory.items.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Category</label>
                    <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Item / Asset Name</label>
                    <input type="text" name="item_name" required placeholder="Raspberry Pi 5 (8GB RAM)" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Stock Quantity</label>
                        <input type="number" name="quantity" value="10" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Unit Price ($)</label>
                        <input type="number" step="0.01" name="unit_price" value="85.00" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="itemModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Log Asset</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
