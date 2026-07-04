<nav x-data="{ open: false }" class="bg-white/60 backdrop-blur-xl border border-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-[2rem] w-full z-50">
    <!-- Primary Navigation Menu -->
    <div class="px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center w-full">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-extrabold text-2xl text-gray-900 tracking-tight flex items-center gap-3 group">
                        <span class="bg-gradient-to-br from-violet-600 to-fuchsia-600 w-12 h-12 rounded-2xl flex items-center justify-center text-white font-black shadow-lg shadow-violet-500/30 group-hover:scale-105 transition-transform duration-300">R</span>
                        RiseStar<span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-fuchsia-600">AI</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ms-12 space-x-2 w-full justify-start">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(auth()->user()->role === 'student')
                        <x-nav-link :href="route('student.tests.index')" :active="request()->routeIs('student.tests.*')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('student.tests.*') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('Aptitude Tests') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.careers.index')" :active="request()->routeIs('student.careers.*')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('student.careers.*') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('Career Paths') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.skill-gap.index')" :active="request()->routeIs('student.skill-gap.*')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('student.skill-gap.*') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('Skill Gap') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.resume.index')" :active="request()->routeIs('student.resume.*')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('student.resume.*') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('AI Resume Builder') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.interview.index')" :active="request()->routeIs('student.interview.*')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('student.interview.*') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('Mock Interview') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('admin.questions')" :active="request()->routeIs('admin.questions')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('admin.questions') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('Questions') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.careers')" :active="request()->routeIs('admin.careers')" class="text-sm font-bold px-5 py-3 rounded-2xl transition-all {{ request()->routeIs('admin.careers') ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-white/80' }}">
                            {{ __('Careers DB') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                
                <!-- Gamification Widget -->
                @if(auth()->user()->role === 'student')
                    <div class="flex items-center bg-white/80 border border-gray-100 rounded-2xl p-1.5 shadow-sm group hover:shadow-md transition-all">
                        <div class="pl-3 pr-2 py-1 flex flex-col justify-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Level {{ auth()->user()->level }}</span>
                            
                            <!-- Progress Bar -->
                            @php
                                $progress = auth()->user()->getLevelProgressPercentage();
                            @endphp
                            <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-violet-500 to-fuchsia-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-100 to-fuchsia-100 flex items-center justify-center text-lg shadow-inner border border-violet-50 group-hover:scale-110 transition-transform">
                            🌟
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-bold rounded-2xl text-gray-700 bg-white/80 hover:bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 mr-3 flex items-center justify-center text-gray-500">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-1">
                            <x-dropdown-link :href="route('profile.edit')" class="rounded-xl font-bold text-gray-600 hover:bg-gray-50">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="rounded-xl font-bold text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-3 rounded-2xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/90 backdrop-blur-md rounded-b-[2rem] border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-xl font-bold">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(auth()->user()->role === 'student')
                <x-responsive-nav-link :href="route('student.tests.index')" :active="request()->routeIs('student.tests.*')" class="rounded-xl font-bold">
                    {{ __('Aptitude Tests') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.careers.index')" :active="request()->routeIs('student.careers.*')" class="rounded-xl font-bold">
                    {{ __('Career Paths') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-gray-100 px-4">
            <div class="flex items-center px-4 mb-4">
                <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="rounded-xl font-bold">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="rounded-xl font-bold text-rose-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
