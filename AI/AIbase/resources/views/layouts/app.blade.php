<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'RiseStar AI') }}</title>
        {{-- Preconnect for faster font loading --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }
            .sidebar-link.active { background: rgba(255,255,255,0.15); }
            .sidebar-link:hover { background: rgba(255,255,255,0.1); }
            ::-webkit-scrollbar { width: 4px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 99px; }
            /* Instant page transitions */
            .page-content { animation: fadeIn 0.15s ease-out; }
            @keyframes fadeIn { from { opacity: 0.7; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
            /* Top loading bar */
            #nprogress-bar {
                position: fixed; top: 0; left: 0; height: 3px; width: 0%;
                background: linear-gradient(to right, #6366f1, #06b6d4);
                z-index: 9999; transition: width 0.3s ease;
                box-shadow: 0 0 8px rgba(99,102,241,0.6);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#F4F5F7] text-gray-900">

        <div class="flex min-h-screen" x-data="{ mobileOpen: false }">

            {{-- ===== SIDEBAR ===== --}}
            <aside class="hidden lg:flex flex-col fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-[#1E1B4B] to-[#312E81] z-40 shadow-2xl">

                {{-- Logo --}}
                <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center font-black text-white text-lg shadow-lg shadow-orange-500/30">R</div>
                    <div>
                        <h1 class="font-black text-white text-lg leading-tight tracking-tight">RiseStar<span class="text-yellow-400">AI</span></h1>
                        <p class="text-indigo-300 text-[10px] font-bold uppercase tracking-widest">Career Platform</p>
                    </div>
                </div>

                {{-- XP / Level Widget --}}
                @auth
                    @php
                        $u = auth()->user();
                        // Cache XP/level in session to avoid DB hit every page
                        if (!session()->has('_u_xp') || session('_u_tick', 0) < time() - 30) {
                            session(['_u_xp' => $u->xp, '_u_level' => $u->level, '_u_name' => $u->name, '_u_email' => $u->email, '_u_tick' => time()]);
                        }
                        $uXp    = session('_u_xp', $u->xp);
                        $uLevel = session('_u_level', $u->level);
                        $uName  = session('_u_name', $u->name);
                        $uEmail = session('_u_email', $u->email);
                        $progress = $u->getLevelProgressPercentage();
                        $xpNext  = $u->getXpForNextLevel();
                    @endphp
                    <div class="px-5 py-5 border-b border-white/10">
                        <div class="bg-white/10 rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-indigo-300 text-[10px] uppercase tracking-widest font-bold">Your Level</p>
                                    <p class="text-white font-black text-2xl leading-none">{{ $uLevel }} <span class="text-yellow-400">✦</span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-indigo-300 text-[10px] uppercase tracking-widest font-bold">Total XP</p>
                                    <p class="text-white font-black text-2xl leading-none">{{ $uXp }}</p>
                                </div>
                            </div>
                            <div class="w-full bg-white/10 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 transition-all duration-700" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="text-indigo-300 text-[10px] font-bold mt-2">{{ $uXp }} / {{ $xpNext }} XP to next level</p>
                        </div>
                    </div>
                @endauth

                {{-- Nav Links --}}
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all text-white group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.dashboard') ? 'bg-yellow-400 text-indigo-900' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-yellow-400 group-hover:text-indigo-900">🏠</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.dashboard') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">Dashboard</span>
                    </a>
                    <a href="{{ route('student.tests.index') }}" class="sidebar-link {{ request()->routeIs('student.tests.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.tests.*') ? 'bg-fuchsia-400 text-white' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-fuchsia-400 group-hover:text-white">🎯</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.tests.*') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">Aptitude Tests</span>
                    </a>
                    <a href="{{ route('student.careers.index') }}" class="sidebar-link {{ request()->routeIs('student.careers.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.careers.*') ? 'bg-cyan-400 text-indigo-900' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-cyan-400 group-hover:text-indigo-900">🗺️</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.careers.*') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">Career Paths</span>
                    </a>
                    <a href="{{ route('student.skill-gap.index') }}" class="sidebar-link {{ request()->routeIs('student.skill-gap.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.skill-gap.*') ? 'bg-emerald-400 text-white' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-emerald-400 group-hover:text-white">🧠</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.skill-gap.*') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">Skill Gap</span>
                    </a>
                    <a href="{{ route('student.resume.index') }}" class="sidebar-link {{ request()->routeIs('student.resume.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.resume.*') ? 'bg-orange-400 text-white' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-orange-400 group-hover:text-white">📄</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.resume.*') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">AI Resume</span>
                    </a>
                    <a href="{{ route('student.interview.index') }}" class="sidebar-link {{ request()->routeIs('student.interview.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.interview.*') ? 'bg-rose-400 text-white' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-rose-400 group-hover:text-white">🎤</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.interview.*') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">Mock Interview</span>
                    </a>
                    <a href="{{ route('student.blueprint') }}" class="sidebar-link {{ request()->routeIs('student.blueprint*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-2xl transition-all group">
                        <span class="w-9 h-9 rounded-xl {{ request()->routeIs('student.blueprint*') ? 'bg-violet-400 text-white' : 'bg-white/10 text-white' }} flex items-center justify-center text-base transition-all group-hover:bg-violet-400 group-hover:text-white">✨</span>
                        <span class="font-bold text-sm {{ request()->routeIs('student.blueprint*') ? 'text-white' : 'text-indigo-200' }} group-hover:text-white">AI Blueprint</span>
                    </a>
                </nav>

                {{-- Profile + Logout --}}
                <div class="px-4 py-4 border-t border-white/10">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 transition-all group">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center font-black text-white text-sm shadow-md">
                            {{ strtoupper(substr($uName, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-bold text-sm truncate">{{ $uName }}</p>
                            <p class="text-indigo-300 text-[10px] truncate">{{ $uEmail }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl hover:bg-rose-500/20 transition-all text-left group">
                            <span class="w-9 h-9 rounded-xl bg-white/5 group-hover:bg-rose-500/20 flex items-center justify-center text-base transition-all">🚪</span>
                            <span class="font-bold text-sm text-indigo-300 group-hover:text-rose-400">Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ===== MOBILE HEADER ===== --}}
            <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-[#1E1B4B] flex items-center justify-between px-4 h-16 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center font-black text-white text-sm">R</div>
                    <span class="font-black text-white text-lg">RiseStar<span class="text-yellow-400">AI</span></span>
                </div>
                <button @click="mobileOpen = !mobileOpen" class="text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Sidebar Overlay --}}
            <div x-show="mobileOpen" @click="mobileOpen = false" class="lg:hidden fixed inset-0 bg-black/50 z-40 backdrop-blur-sm" style="display:none;"></div>
            <div x-show="mobileOpen" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-[#1E1B4B] z-50 shadow-2xl overflow-y-auto" style="display:none;">
                <div class="p-4 pt-16">
                    <nav class="space-y-1">
                        @if(auth()->check() && auth()->user()->role === 'student')
                            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-indigo-200 font-bold text-sm">🏠 Dashboard</a>
                            <a href="{{ route('student.tests.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-indigo-200 font-bold text-sm">🎯 Aptitude Tests</a>
                            <a href="{{ route('student.careers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-indigo-200 font-bold text-sm">🗺️ Career Paths</a>
                            <a href="{{ route('student.skill-gap.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-indigo-200 font-bold text-sm">🧠 Skill Gap</a>
                            <a href="{{ route('student.resume.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-indigo-200 font-bold text-sm">📄 AI Resume</a>
                            <a href="{{ route('student.interview.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-indigo-200 font-bold text-sm">🎤 Mock Interview</a>
                        @endif
                    </nav>
                </div>
            </div>

            {{-- ===== MAIN CONTENT ===== --}}
            <div class="flex-1 flex flex-col lg:ml-64 min-h-screen">

                {{-- Top Bar --}}
                <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-gray-100 px-6 lg:px-8 h-16 flex items-center justify-between shadow-sm">
                    <div class="lg:hidden w-8"></div>
                    @isset($header)
                        <div>{{ $header }}</div>
                    @else
                        <div class="text-sm text-gray-400 font-bold uppercase tracking-widest hidden lg:block">
                            {{ ucfirst(explode('.', request()->route()->getName())[1] ?? 'Dashboard') }}
                        </div>
                    @endisset
                    <div class="flex items-center gap-3">
                        @auth
                            <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-2xl">
                                <span class="text-yellow-500">⭐</span>
                                <span class="font-black text-indigo-700 text-sm">{{ session('_u_xp', Auth::user()->xp ?? 0) }} XP</span>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center font-black text-white text-sm shadow-md">
                                {{ strtoupper(substr(session('_u_name', Auth::user()->name), 0, 1)) }}
                            </div>
                        @endauth
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 p-6 lg:p-8 pt-20 lg:pt-6 page-content">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @auth
            @if(!request()->routeIs('student.tests.show') && !request()->routeIs('student.tests.result'))
                @livewire('student.chat-counselor')
            @endif
        @endauth

        {{-- Gamification Toasts --}}
        @if(session('gamification_xp') || session('gamification_levelup') || session('gamification_badges'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="fixed bottom-6 right-6 z-50 flex flex-col gap-2">

                @if(session('gamification_levelup'))
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3">
                        <span class="text-2xl">🏆</span>
                        <div>
                            <p class="font-black text-sm uppercase tracking-wider opacity-80">Level Up!</p>
                            <p class="font-bold">{{ session('gamification_levelup') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('gamification_badges'))
                    @foreach(session('gamification_badges') as $badge)
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3">
                            <span class="text-2xl">{{ $badge['icon'] }}</span>
                            <div>
                                <p class="font-black text-sm uppercase tracking-wider opacity-80">Badge Unlocked!</p>
                                <p class="font-bold">{{ $badge['name'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(session('gamification_xp'))
                    <div class="bg-gradient-to-r from-indigo-500 to-violet-600 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3">
                        <span class="text-2xl">⚡</span>
                        <p class="font-bold">{{ session('gamification_xp') }}</p>
                    </div>
                @endif
            </div>
        @endif

        @livewireScripts
        <script>
            // Top loading bar on navigation
            const bar = document.createElement('div');
            bar.id = 'nprogress-bar';
            document.body.appendChild(bar);

            let timer;
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.startsWith('#') &&
                    !link.href.startsWith('javascript') &&
                    link.target !== '_blank' &&
                    link.hostname === location.hostname) {
                    bar.style.width = '0%';
                    bar.style.opacity = '1';
                    clearTimeout(timer);
                    // Animate to 80% quickly
                    requestAnimationFrame(() => { bar.style.width = '70%'; });
                    timer = setTimeout(() => { bar.style.width = '90%'; }, 300);
                }
            });

            // Complete bar on page load
            window.addEventListener('load', () => {
                bar.style.width = '100%';
                setTimeout(() => { bar.style.opacity = '0'; bar.style.width = '0%'; }, 300);
            });

            // Sidebar active link instant highlight
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', function() {
                    document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        </script>
    </body>
</html>
