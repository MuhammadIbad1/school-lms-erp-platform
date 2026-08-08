@extends('layouts.app')

@section('title', 'Library Management')
@section('header', 'Library & Book Circulation')
@section('subheader', 'Book Inventory, ISBN Catalog, and Borrowing Ledger')

@section('content')
<div class="space-y-8" x-data="{ bookModal: false, issueModal: false }">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
            {{ $books->sum('quantity') }} Books in Library Stock
        </span>
        <div class="flex items-center space-x-3">
            <button @click="issueModal = true" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                + Issue Book to Student/Teacher
            </button>
            <button @click="bookModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                + Add Book to Catalog
            </button>
        </div>
    </div>

    <!-- Active Circulation Ledger -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Active Borrowing Ledger</h3>
                <p class="text-xs text-slate-500">Track issued books and due dates</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4">Book Title</th>
                        <th class="px-5 py-4">Borrower</th>
                        <th class="px-5 py-4">Issue Date</th>
                        <th class="px-5 py-4">Due Date</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($issues as $issue)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">{{ $issue->book->title }}</td>
                            <td class="px-5 py-4 font-semibold text-indigo-600">{{ $issue->user->name }}</td>
                            <td class="px-5 py-4">{{ $issue->issue_date->format('M d, Y') }}</td>
                            <td class="px-5 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $issue->due_date->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $issue->status === 'returned' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $issue->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($issue->status === 'issued')
                                    <form method="POST" action="{{ route('admin.library.return', $issue) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                                            Return Book
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-xs font-semibold">Returned</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No books currently on loan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Book Catalog Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($books as $book)
            <div class="glass-card p-6 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 font-mono">ISBN: {{ $book->isbn }}</span>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white mt-2">{{ $book->title }}</h4>
                    <p class="text-xs text-slate-500 mt-1">Author: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $book->author }}</span></p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="font-bold text-emerald-600">{{ $book->quantity }} in Stock</span>
                    <span class="font-mono text-slate-400">Rack: {{ $book->rack_number ?? 'General' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ================= MODAL: ADD BOOK ================= -->
    <div x-show="bookModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="bookModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Book to Library</h3>
            <form method="POST" action="{{ route('admin.library.books.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Book Title</label>
                    <input type="text" name="title" required placeholder="Clean Architecture" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">ISBN Number</label>
                    <input type="text" name="isbn" required placeholder="978-0134494166" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Author</label>
                    <input type="text" name="author" required placeholder="Robert C. Martin" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Quantity</label>
                        <input type="number" name="quantity" value="5" min="1" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Rack Number</label>
                        <input type="text" name="rack_number" placeholder="CS-RACK-01" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium font-mono">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="bookModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Add Book</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: ISSUE BOOK ================= -->
    <div x-show="issueModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="issueModal = false" class="glass-card w-full max-w-md p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Issue Book</h3>
            <form method="POST" action="{{ route('admin.library.issue') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Select Book</label>
                    <select name="book_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($books as $b)
                            <option value="{{ $b->id }}" {{ $b->quantity < 1 ? 'disabled' : '' }}>{{ $b->title }} ({{ $b->quantity }} available)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Borrower</label>
                    <select name="user_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->primary_role }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Issue Date</label>
                        <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="issueModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Issue Book</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
