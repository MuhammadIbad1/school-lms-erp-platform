<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - EduNova School LMS & ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px) saturate(200%);
            -webkit-backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .ambient-bg {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.3) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.25) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(14, 165, 233, 0.2) 0px, transparent 50%);
        }
    </style>
</head>
<body class="h-full ambient-bg flex items-center justify-center p-4 sm:p-6" x-data="{
    email: '{{ old('email', 'admin@school.com') }}',
    password: 'password123',
    setRole(rEmail) {
        this.email = rEmail;
        this.password = 'password123';
    }
}">

    <div class="w-full max-w-xl">
        
        <!-- Header Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 text-white shadow-2xl shadow-indigo-500/40 mb-4 transform hover:scale-105 transition">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight font-display">EduNova <span class="text-indigo-400">School LMS & ERP</span></h1>
            <p class="text-slate-400 text-sm mt-1">Unified Multi-Portal Academic Operations & E-Learning Platform</p>
        </div>

        <!-- Glassmorphic Login Card -->
        <div class="glass-card rounded-3xl p-6 sm:p-10">
            
            <!-- Alert Messages -->
            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-rose-800 bg-rose-100/90 rounded-2xl border border-rose-200 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-emerald-800 bg-emerald-100/90 rounded-2xl border border-emerald-200 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1-Click Quick Demo Switcher -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">1-Click Quick Demo Login Switcher</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button type="button" @click="setRole('admin@school.com')" class="p-2.5 rounded-xl border border-indigo-200 bg-indigo-50/80 hover:bg-indigo-100 text-indigo-800 text-xs font-bold transition flex flex-col items-center">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 mb-1"></span>
                        Super Admin
                    </button>
                    <button type="button" @click="setRole('teacher@school.com')" class="p-2.5 rounded-xl border border-purple-200 bg-purple-50/80 hover:bg-purple-100 text-purple-800 text-xs font-bold transition flex flex-col items-center">
                        <span class="w-2 h-2 rounded-full bg-purple-600 mb-1"></span>
                        Faculty Teacher
                    </button>
                    <button type="button" @click="setRole('student@school.com')" class="p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/80 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition flex flex-col items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 mb-1"></span>
                        Student Portal
                    </button>
                    <button type="button" @click="setRole('parent@school.com')" class="p-2.5 rounded-xl border border-amber-200 bg-amber-50/80 hover:bg-amber-100 text-amber-800 text-xs font-bold transition flex flex-col items-center">
                        <span class="w-2 h-2 rounded-full bg-amber-600 mb-1"></span>
                        Parent Guardian
                    </button>
                </div>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" x-model="email" required autofocus
                               class="w-full px-4 py-3.5 rounded-2xl bg-white/90 border border-slate-300 text-slate-900 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition shadow-sm"
                               placeholder="user@school.com">
                    </div>
                    @error('email')
                        <p class="text-rose-600 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" x-model="password" required
                               class="w-full px-4 py-3.5 rounded-2xl bg-white/90 border border-slate-300 text-slate-900 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition shadow-sm"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-rose-600 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span class="ml-2 text-xs font-semibold text-slate-600">Remember session</span>
                    </label>
                    <span class="text-xs font-semibold text-indigo-600">Demo Password: password123</span>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-bold shadow-xl shadow-indigo-600/30 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                        <span>Authenticate & Access Portal</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

            </form>

        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            &copy; 2026 EduNova ERP Engine. Built on PHP Laravel 12 & MySQL.
        </div>

    </div>

</body>
</html>
