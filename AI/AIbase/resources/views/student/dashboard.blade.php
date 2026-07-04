<x-app-layout>

    {{-- Welcome Header --}}
    <div class="mb-8">
        <p class="text-indigo-400 font-bold text-sm uppercase tracking-widest mb-1">👋 Good {{ now()->format('G') < 12 ? 'Morning' : (now()->format('G') < 17 ? 'Afternoon' : 'Evening') }}</p>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ Auth::user()->name }} <span class="text-yellow-500">✦</span></h1>
        <p class="text-gray-500 font-medium mt-1">Here's your career progress summary for today.</p>
    </div>

    {{-- Stats Row --}}
    @php
        $u = Auth::user();
        $progress = $u->getLevelProgressPercentage();
        $badges = $u->earned_badges ?? [];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-indigo-500 to-violet-600 rounded-3xl p-5 text-white shadow-xl shadow-indigo-500/20 col-span-1">
            <p class="text-indigo-200 text-[10px] uppercase tracking-widest font-black mb-2">Current Level</p>
            <p class="text-4xl font-black">{{ $u->level }}</p>
            <p class="text-indigo-300 text-xs font-bold mt-1">{{ $progress }}% to next</p>
        </div>
        <div class="bg-gradient-to-br from-orange-400 to-rose-500 rounded-3xl p-5 text-white shadow-xl shadow-orange-400/20 col-span-1">
            <p class="text-orange-100 text-[10px] uppercase tracking-widest font-black mb-2">Total XP</p>
            <p class="text-4xl font-black">{{ $u->xp }}</p>
            <p class="text-orange-200 text-xs font-bold mt-1">Keep going! ⚡</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 col-span-1">
            <p class="text-gray-400 text-[10px] uppercase tracking-widest font-black mb-2">Tests Done</p>
            <p class="text-4xl font-black text-gray-900">{{ $stats['tests_taken'] }}</p>
            <p class="text-gray-400 text-xs font-bold mt-1">Aptitude tests</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 col-span-1">
            <p class="text-gray-400 text-[10px] uppercase tracking-widest font-black mb-2">Badges</p>
            <p class="text-4xl font-black text-gray-900">{{ count($badges) }}</p>
            <p class="text-gray-400 text-xs font-bold mt-1">Achievements 🏅</p>
        </div>
    </div>

    {{-- XP Progress Bar --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-black text-gray-900">XP Progress</h3>
                <p class="text-sm text-gray-500 font-medium">Level {{ $u->level }} → Level {{ $u->level + 1 }}</p>
            </div>
            <span class="bg-yellow-100 text-yellow-700 font-black text-sm px-4 py-2 rounded-2xl border border-yellow-200">{{ $u->xp }} / {{ $u->getXpForNextLevel() }} XP</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
            <div class="h-4 rounded-full bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 transition-all duration-1000 relative overflow-hidden" style="width: {{ $progress }}%">
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- Feature Cards --}}
    <h2 class="font-black text-gray-900 text-lg mb-4 flex items-center gap-2">🚀 Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">

        <a href="{{ route('student.tests.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
            <div class="w-12 h-12 rounded-2xl bg-fuchsia-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🎯</div>
            <div>
                <h3 class="font-black text-gray-900 text-base">Aptitude Test</h3>
                <p class="text-gray-500 text-sm font-medium mt-1">Evaluate your cognitive strengths with AI-powered tests.</p>
            </div>
            <div class="mt-auto flex items-center text-fuchsia-600 font-bold text-sm group-hover:gap-3 gap-2 transition-all">
                Start Now <span>→</span>
            </div>
        </a>

        <a href="{{ route('student.skill-gap.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🧠</div>
            <div>
                <h3 class="font-black text-gray-900 text-base">Skill-Gap Analysis</h3>
                <p class="text-gray-500 text-sm font-medium mt-1">Find what skills you're missing for your dream career.</p>
            </div>
            <div class="mt-auto flex items-center text-emerald-600 font-bold text-sm group-hover:gap-3 gap-2 transition-all">
                Analyze <span>→</span>
            </div>
        </a>

        <a href="{{ route('student.resume.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📄</div>
            <div>
                <h3 class="font-black text-gray-900 text-base">AI Resume Builder</h3>
                <p class="text-gray-500 text-sm font-medium mt-1">Generate a professional resume tailored to your target role.</p>
            </div>
            <div class="mt-auto flex items-center text-orange-600 font-bold text-sm group-hover:gap-3 gap-2 transition-all">
                Build Resume <span>→</span>
            </div>
        </a>

        <a href="{{ route('student.interview.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🎤</div>
            <div>
                <h3 class="font-black text-gray-900 text-base">Mock Interview</h3>
                <p class="text-gray-500 text-sm font-medium mt-1">Practice answering real interview questions with AI feedback.</p>
            </div>
            <div class="mt-auto flex items-center text-rose-600 font-bold text-sm group-hover:gap-3 gap-2 transition-all">
                Practice <span>→</span>
            </div>
        </a>

        <a href="{{ route('student.careers.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
            <div class="w-12 h-12 rounded-2xl bg-cyan-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🗺️</div>
            <div>
                <h3 class="font-black text-gray-900 text-base">Career Paths</h3>
                <p class="text-gray-500 text-sm font-medium mt-1">Explore hundreds of career options with salary data and roadmaps.</p>
            </div>
            <div class="mt-auto flex items-center text-cyan-600 font-bold text-sm group-hover:gap-3 gap-2 transition-all">
                Explore <span>→</span>
            </div>
        </a>

        <a href="{{ route('student.blueprint') }}" class="group bg-gradient-to-br from-indigo-500 to-violet-600 rounded-3xl p-6 shadow-xl shadow-indigo-500/20 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">✨</div>
            <div>
                <h3 class="font-black text-white text-base">AI Career Blueprint</h3>
                <p class="text-indigo-200 text-sm font-medium mt-1">Get your fully personalized career plan powered by Gemini AI.</p>
            </div>
            <div class="mt-auto flex items-center text-white font-bold text-sm group-hover:gap-3 gap-2 transition-all">
                Generate Plan <span>→</span>
            </div>
        </a>

    </div>

    {{-- Recent Tests + Badges --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Recent Tests --}}
        <div>
            <h2 class="font-black text-gray-900 text-lg mb-4 flex items-center gap-2">📊 Recent Test Results</h2>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                @forelse($attempts->take(5) as $attempt)
                    <a href="{{ route('student.tests.result', $attempt->id) }}" class="flex items-center justify-between p-5 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors group">
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-indigo-600 transition-colors">{{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('d M Y') : 'N/A' }}</p>
                            <p class="text-gray-400 text-xs font-medium mt-0.5">{{ $attempt->score }} correct answers</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                @php $pct = $attempt->percentage ?? 0; @endphp
                                <span class="font-black text-lg {{ $pct >= 80 ? 'text-emerald-600' : ($pct >= 50 ? 'text-amber-600' : 'text-rose-600') }}">{{ $pct }}%</span>
                            </div>
                            <div class="w-8 h-8 rounded-full {{ $pct >= 80 ? 'bg-emerald-100 text-emerald-600' : ($pct >= 50 ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600') }} flex items-center justify-center text-sm font-bold">
                                {{ $pct >= 80 ? '🏆' : ($pct >= 50 ? '👍' : '💪') }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-10 text-center">
                        <div class="text-4xl mb-3">📝</div>
                        <p class="font-bold text-gray-900">No tests taken yet</p>
                        <p class="text-gray-500 text-sm mt-1">Take your first aptitude test to see results.</p>
                        <a href="{{ route('student.tests.index') }}" class="mt-4 inline-block bg-indigo-600 text-white font-bold text-sm px-5 py-2.5 rounded-2xl hover:bg-indigo-500 transition-colors">Start Test</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Earned Badges --}}
        <div>
            <h2 class="font-black text-gray-900 text-lg mb-4 flex items-center gap-2">🏅 Earned Badges</h2>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 min-h-[200px]">
                @if(empty($badges))
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="text-4xl mb-3 grayscale opacity-50">🏅</div>
                        <p class="font-bold text-gray-900">No badges yet</p>
                        <p class="text-gray-500 text-sm mt-1">Complete tests and features to earn badges!</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($badges as $badge)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/50 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-violet-100 flex items-center justify-center text-xl shrink-0">
                                    {{ $badge['icon'] ?? '🏅' }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-xs truncate">{{ $badge['name'] ?? 'Badge' }}</p>
                                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest mt-0.5">Earned</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- AI Recommendations --}}
    @if(count($recommendations) > 0)
    <div class="mb-8">
        <h2 class="font-black text-gray-900 text-lg mb-4 flex items-center gap-2">🤖 AI Career Matches
            <span class="text-[10px] bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full font-black uppercase tracking-widest">Gemini AI</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($recommendations as $career)
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group">
                    <span class="text-[10px] text-violet-600 font-black tracking-widest uppercase bg-violet-50 px-3 py-1 rounded-xl">{{ $career->category }}</span>
                    <h4 class="font-black text-gray-900 text-base mt-4 mb-2">{{ $career->title }}</h4>
                    <p class="text-sm text-gray-500 font-medium leading-relaxed line-clamp-2">{{ Str::limit($career->description, 100) }}</p>
                    <a href="{{ route('student.careers.index') }}" class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-violet-600 hover:text-violet-700 group-hover:gap-3 transition-all">
                        Explore <span>→</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</x-app-layout>
