@extends('layouts.app')

@section('title', 'Notice Bulletin Board')
@section('header', 'School Notice & Broadcast Board')
@section('subheader', 'Publish Targeted Announcements to Faculty, Students, and Parents')

@section('content')
<div class="space-y-8" x-data="{ noticeModal: false }">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold text-xs">
            {{ $notices->total() }} Active Institutional Bulletins
        </span>
        <button @click="noticeModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            + Broadcast New Notice
        </button>
    </div>

    <!-- Notices Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($notices as $notice)
            <div class="glass-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $notice->target_role === 'all' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : ($notice->target_role === 'teacher' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                            Target Audience: {{ strtoupper($notice->target_role) }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">{{ $notice->created_at->format('M d, Y') }}</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $notice->title }}</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed whitespace-pre-line">{{ $notice->content }}</p>
                </div>
                
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400">By: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $notice->author->name }}</span></span>
                    <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" onsubmit="return confirm('Remove this notice?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-2 p-10 text-center text-slate-400 glass-card">
                No active announcements published.
            </div>
        @endforelse
    </div>

    <!-- ================= MODAL: BROADCAST NOTICE ================= -->
    <div x-show="noticeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="noticeModal = false" class="glass-card w-full max-w-lg p-6 bg-white dark:bg-slate-900 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Broadcast Notice Announcement</h3>
            <form method="POST" action="{{ route('admin.notices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Notice Headline / Title</label>
                    <input type="text" name="title" required placeholder="Annual Sports & Science Expo 2026" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Target Audience</label>
                    <select name="target_role" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                        <option value="all">Entire School (Everyone)</option>
                        <option value="teacher">Faculty Teachers Only</option>
                        <option value="student">Enrolled Students Only</option>
                        <option value="parent">Parents & Guardians Only</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Detailed Content</label>
                    <textarea name="content" rows="4" required placeholder="Write the complete announcement text..." class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="noticeModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Broadcast Notice</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
