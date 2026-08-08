<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EduNova LMS') }} - @yield('title', 'Smart Management Platform')</title>

    <!-- Google Fonts (Plus Jakarta Sans & Space Grotesk) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN for high-end styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Glassmorphism System */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        }

        .dark .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .dark .glass-nav {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(30, 41, 59, 0.8);
        }

        .flat-table-wrapper {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .dark .flat-table-wrapper {
            background-color: #0F172A;
            border: 1px solid #1E293B;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        /* Ambient subtle background glow */
        .ambient-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 10% 10%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(236, 72, 153, 0.06) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(14, 165, 233, 0.05) 0px, transparent 50%);
        }

        .dark .ambient-bg {
            background-color: #030712;
            background-image: 
                radial-gradient(at 10% 10%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(236, 72, 153, 0.10) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(14, 165, 233, 0.08) 0px, transparent 50%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.6);
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-full-width { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>

    <script>
        // Init theme
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Web Audio notification chime
        function playChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.4);
            } catch(e) {}
        }
    </script>
</head>
<body class="h-full text-slate-800 dark:text-slate-100 ambient-bg transition-colors duration-200" x-data="{ sidebarOpen: false, profileDropdown: false, notifOpen: false, activeToastNotice: null }">

    @php
        $userNotices = collect();
        if (auth()->check()) {
            $userRole = auth()->user()->primary_role;
            $userNotices = \App\Models\Notice::where(function($q) use ($userRole) {
                $q->where('target_role', 'all')
                  ->orWhere('target_role', $userRole);
            })->latest()->take(6)->get();
        }
    @endphp

    <!-- Real-Time Interactive Toast Notification Popup -->
    @if(auth()->check() && $userNotices->isNotEmpty())
        @php
            $latestNotice = $userNotices->first();
        @endphp
        <div x-data="{
                dismissed: localStorage.getItem('dismissed_notice_{{ $latestNotice->id }}') === 'true',
                visible: false,
                init() {
                    if (!this.dismissed) {
                        setTimeout(() => {
                            this.visible = true;
                            playChime();
                        }, 1200);
                    }
                },
                dismiss() {
                    this.visible = false;
                    localStorage.setItem('dismissed_notice_{{ $latestNotice->id }}', 'true');
                }
             }"
             x-show="visible"
             x-transition:enter="transform ease-out duration-400 transition"
             x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:translate-x-6"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak
             class="no-print fixed bottom-6 right-6 z-50 max-w-md w-full glass-card p-5 bg-white/95 dark:bg-slate-900/95 shadow-2xl border-2 border-indigo-500/40 dark:border-indigo-500/30">
            
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white font-bold flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-600/30 animate-pulse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ strtoupper($latestNotice->target_role) }} BROADCAST
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $latestNotice->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-900 dark:text-white mt-1">{{ $latestNotice->title }}</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">{{ Str::limit($latestNotice->content, 120) }}</p>
                    
                    <div class="mt-3 flex items-center justify-end space-x-2">
                        <button @click="dismiss()" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Dismiss
                        </button>
                        <button @click="notifOpen = true; dismiss();" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                            View All Notices
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Alert System -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-emerald-800 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shadow-2xl backdrop-blur-md transition transform ease-out duration-300">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
            <button @click="show = false" class="ml-4 text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
             class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-rose-800 rounded-2xl bg-rose-50 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shadow-2xl backdrop-blur-md transition transform ease-out duration-300">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm font-semibold">
                {{ session('error') ?? $errors->first() }}
            </div>
            <button @click="show = false" class="ml-4 text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <div class="flex h-full overflow-hidden">
        
        <!-- ==================== SIDEBAR ==================== -->
        <aside class="no-print fixed inset-y-0 left-0 z-40 w-72 transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 flex flex-col glass-card m-3 border-r-0 md:border-r border-white/40 dark:border-slate-800/60"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- School Brand / Logo -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-slate-200/60 dark:border-slate-800/60">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold font-display tracking-tight text-slate-900 dark:text-white">EduNova <span class="text-indigo-600 dark:text-indigo-400 font-extrabold">ERP</span></span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Smart School LMS</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">

                @auth
                    @if(auth()->user()->hasRole(['super-admin', 'admin']))
                        <!-- ================= SUPER ADMIN PORTAL ================= -->
                        <div class="px-3 py-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Main Operations</div>
                        
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Executive Overview
                        </a>

                        <a href="{{ route('admin.academics.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.academics.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Academics & Structure
                        </a>

                        <a href="{{ route('admin.students.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Student Enrollment
                        </a>

                        <a href="{{ route('admin.teachers.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.teachers.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Faculty HR & Teaching
                        </a>

                        <div class="px-3 pt-4 pb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Finance & Security</div>

                        <a href="{{ route('admin.fees.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.fees.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Fee Engine & Invoices
                        </a>

                        <a href="{{ route('admin.payroll.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.payroll.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                            Faculty Payroll Slips
                        </a>

                        <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            RBAC Role Matrix
                        </a>

                        <div class="px-3 pt-4 pb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Auxiliary Subsystems</div>

                        <a href="{{ route('admin.library.index') }}" class="flex items-center px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.library.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Library Catalog
                        </a>

                        <a href="{{ route('admin.transport.index') }}" class="flex items-center px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.transport.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Transport Fleet
                        </a>

                        <a href="{{ route('admin.hostel.index') }}" class="flex items-center px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.hostel.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Hostel Residences
                        </a>

                        <a href="{{ route('admin.inventory.index') }}" class="flex items-center px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.inventory.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Inventory Assets
                        </a>

                        <a href="{{ route('admin.notices.index') }}" class="flex items-center px-4 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.notices.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            Notice Bulletins
                        </a>

                    @elseif(auth()->user()->hasRole('teacher'))
                        <!-- ================= TEACHER WORKSPACE ================= -->
                        <div class="px-3 py-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Faculty Workspace</div>

                        <a href="{{ route('teacher.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Teacher Dashboard
                        </a>

                        <a href="{{ route('teacher.attendance.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('teacher.attendance.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Daily Attendance Matrix
                        </a>

                        <a href="{{ route('teacher.gradebook.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('teacher.gradebook.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Exam Assessment Gradebook
                        </a>

                        <a href="{{ route('teacher.lms.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('teacher.lms.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            LMS Studio & Homework
                        </a>

                    @elseif(auth()->user()->hasRole('student'))
                        <!-- ================= STUDENT PORTAL ================= -->
                        <div class="px-3 py-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Student Learning Hub</div>

                        <a href="{{ route('student.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('student.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Student Dashboard
                        </a>

                        <a href="{{ route('student.timetable') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('student.timetable') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Weekly Timetable
                        </a>

                        <a href="{{ route('student.assignments.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('student.assignments.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Homework Submissions
                        </a>

                        <a href="{{ route('student.materials') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('student.materials') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Study Materials
                        </a>

                        <a href="{{ route('student.report-card') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('student.report-card') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Official Report Card
                        </a>

                        <a href="{{ route('student.fees.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('student.fees.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Tuition & Fees
                        </a>

                    @elseif(auth()->user()->hasRole('parent'))
                        <!-- ================= PARENT GUARDIAN PORTAL ================= -->
                        <div class="px-3 py-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Parent Guardian Portal</div>

                        <a href="{{ route('parent.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('parent.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Parent Dashboard
                        </a>

                        <a href="{{ route('parent.attendance') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('parent.attendance') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Child Attendance Log
                        </a>

                        <a href="{{ route('parent.report-card') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('parent.report-card') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Term Report Card
                        </a>

                        <a href="{{ route('parent.timetable') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('parent.timetable') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Child Class Schedule
                        </a>

                        <a href="{{ route('parent.fees.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('parent.fees.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Pay Fees Online
                        </a>
                    @endif
                @endauth

            </div>

            <!-- Footer Meta -->
            <div class="p-4 border-t border-slate-200/60 dark:border-slate-800/60">
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>EduNova v2.4</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                        ● Online
                    </span>
                </div>
            </div>

        </aside>

        <!-- ==================== MAIN WORKSPACE ==================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Sticky Glass Navbar -->
            <header class="no-print sticky top-0 z-30 h-20 glass-nav flex items-center justify-between px-6 md:px-8">
                
                <!-- Left: Mobile Toggle & Page Header -->
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold font-display tracking-tight text-slate-900 dark:text-white">
                            @yield('header', 'Dashboard')
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                            @yield('subheader', 'Enterprise School Operations & LMS')
                        </p>
                    </div>
                </div>

                <!-- Right: Theme Toggle, Notification Bell & Profile -->
                <div class="flex items-center space-x-3 md:space-x-4">
                    
                    <!-- Dark / Light Mode Switcher -->
                    <button @click="
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    " class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <!-- Real-Time Interactive Notification Bell -->
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; playChime();" class="relative p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if($userNotices->isNotEmpty())
                                <span class="absolute top-1.5 right-1.5 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 text-[8px] text-white font-bold items-center justify-center">{{ $userNotices->count() }}</span>
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-3 w-80 sm:w-96 glass-card p-4 shadow-2xl z-50 border border-slate-200/80 dark:border-slate-800/80 max-h-[85vh] overflow-y-auto">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Active Announcements</h4>
                                    <p class="text-[10px] text-slate-400">Targeted for {{ strtoupper(auth()->user()->primary_role) }} audience</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                    {{ $userNotices->count() }} Live
                                </span>
                            </div>

                            <div class="divide-y divide-slate-100 dark:divide-slate-800 my-2">
                                @forelse($userNotices as $un)
                                    <div class="py-3 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 rounded-xl px-2 transition">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 font-mono">
                                                {{ $un->target_role }}
                                            </span>
                                            <span class="text-[10px] text-slate-400">{{ $un->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h5 class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $un->title }}</h5>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ $un->content }}</p>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-slate-400 text-xs">
                                        No active notifications right now.
                                    </div>
                                @endforelse
                            </div>

                            @if(auth()->user()->hasRole(['super-admin', 'admin']))
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-center">
                                    <a href="{{ route('admin.notices.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Manage & Broadcast Notices &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endauth

                    <!-- User Profile Dropdown -->
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center shadow-md">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="text-left hidden lg:block pr-2">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 capitalize">
                                    {{ auth()->user()->primary_role }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-3 w-56 glass-card p-2 shadow-2xl z-50 border border-slate-200/80 dark:border-slate-800/80">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out Safely
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth

                </div>
            </header>

            <!-- Page Body Canvas -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
