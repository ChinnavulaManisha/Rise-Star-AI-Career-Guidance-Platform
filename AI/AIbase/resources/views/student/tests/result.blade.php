<x-app-layout>
    <div class="max-w-4xl mx-auto pb-12">

        {{-- ── HERO BANNER ── --}}
        @php
            $pct = $attempt->percentage ?? 0;
            $grade = $pct >= 80 ? ['label'=>'Excellent','emoji'=>'🏆','from'=>'from-emerald-500','to'=>'to-teal-500','light'=>'bg-emerald-50 text-emerald-700 border-emerald-200']
                   : ($pct >= 60 ? ['label'=>'Good','emoji'=>'👍','from'=>'from-indigo-500','to'=>'to-violet-500','light'=>'bg-indigo-50 text-indigo-700 border-indigo-200']
                   : ($pct >= 40 ? ['label'=>'Average','emoji'=>'💪','from'=>'from-amber-500','to'=>'to-orange-500','light'=>'bg-amber-50 text-amber-700 border-amber-200']
                   : ['label'=>'Needs Work','emoji'=>'📚','from'=>'from-rose-500','to'=>'to-pink-500','light'=>'bg-rose-50 text-rose-700 border-rose-200']));
        @endphp

        <div class="bg-gradient-to-br {{ $grade['from'] }} {{ $grade['to'] }} rounded-3xl p-8 mb-6 relative overflow-hidden shadow-2xl">
            {{-- decorative circles --}}
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">

                {{-- Score Ring --}}
                <div class="shrink-0 flex flex-col items-center">
                    <div class="w-36 h-36 rounded-full bg-white/20 border-4 border-white/40 backdrop-blur flex flex-col items-center justify-center shadow-inner">
                        <span class="text-5xl font-black text-white leading-none">{{ $pct }}</span>
                        <span class="text-white/70 text-sm font-black tracking-widest">%</span>
                    </div>
                    <span class="mt-3 text-white/90 font-black text-sm uppercase tracking-widest">Overall Score</span>
                </div>

                {{-- Info --}}
                <div class="text-center md:text-left">
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-white/70 bg-white/10 px-3 py-1 rounded-full mb-3">
                        Assessment Complete
                    </span>
                    <h1 class="text-3xl font-black text-white mb-2 tracking-tight leading-tight">
                        {{ $grade['emoji'] }} {{ $grade['label'] }} Performance!
                    </h1>
                    <p class="text-white/80 font-medium text-sm mb-5 max-w-md">
                        You scored <strong class="text-white">{{ $attempt->score }} correct answers</strong> and completed in
                        <strong class="text-white">
                            @php $m = floor($attempt->time_taken/60); $s = $attempt->time_taken%60; @endphp
                            {{ $m > 0 ? $m.'m ' : '' }}{{ $s }}s
                        </strong>.
                        Here's your full cognitive breakdown.
                    </p>

                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        <a href="{{ route('student.tests.result.download', $attempt->id) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 border border-white/30 text-white font-black text-sm rounded-2xl hover:bg-white/30 active:scale-95 transition-all backdrop-blur-sm">
                            📄 Download PDF
                        </a>
                        <a href="{{ route('student.tests.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 border border-white/20 text-white/80 font-bold text-sm rounded-2xl hover:bg-white/20 active:scale-95 transition-all">
                            🔄 Retake
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── STAT PILLS ── --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Score</p>
                <p class="text-3xl font-black text-gray-900">{{ $attempt->score }}</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">correct</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Time Taken</p>
                @php
                    $mins = floor($attempt->time_taken / 60);
                    $secs = $attempt->time_taken % 60;
                @endphp
                <p class="text-3xl font-black text-gray-900">
                    @if($mins > 0){{ $mins }}<span class="text-xl">m</span> @endif{{ $secs }}<span class="text-xl">s</span>
                </p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">to complete</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Grade</p>
                <p class="text-3xl font-black text-gray-900">{{ $grade['emoji'] }}</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $grade['label'] }}</p>
            </div>
        </div>

        {{-- ── CATEGORY BREAKDOWN ── --}}
        <div class="bg-white rounded-3xl p-7 mb-6 border border-gray-100 shadow-sm">
            <h2 class="font-black text-gray-900 text-xl mb-6 flex items-center gap-2">
                📊 Aptitude Category Breakdown
            </h2>
            <div class="space-y-5">
                @foreach($attempt->category_scores ?? [] as $category => $scoreData)
                    @php
                        $pctCat = $scoreData['total'] > 0 ? round(($scoreData['correct'] / $scoreData['total']) * 100) : 0;
                        $barColor = $pctCat >= 75 ? 'from-emerald-500 to-teal-400'
                                  : ($pctCat >= 50 ? 'from-indigo-500 to-violet-400'
                                  : 'from-amber-400 to-orange-400');
                        $badge = $pctCat >= 75 ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                               : ($pctCat >= 50 ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                               : 'bg-amber-50 text-amber-700 border-amber-200');
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-800 text-sm">{{ $category }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-400">{{ $scoreData['correct'] }}/{{ $scoreData['total'] }}</span>
                                <span class="text-xs font-black px-2.5 py-0.5 rounded-full border {{ $badge }}">{{ $pctCat }}%</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full bg-gradient-to-r {{ $barColor }} transition-all duration-1000"
                                 style="width: {{ $pctCat }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── CAREER SUGGESTIONS ── --}}
        @if(!empty($suggestions))
        <div class="bg-white rounded-3xl p-7 mb-6 border border-gray-100 shadow-sm">
            <h2 class="font-black text-gray-900 text-xl mb-2 flex items-center gap-2">🎯 Career Paths Matched to Your Results</h2>
            <p class="text-gray-400 text-sm font-medium mb-5">Based on your aptitude scores. Click Explore to go directly to that career.</p>
            <div class="space-y-3">
                @foreach($suggestions as $s)
                <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition-all group">
                    <div class="flex items-center gap-4">
                        <span class="text-2xl">{{ $s['icon'] }}</span>
                        <div>
                            <p class="font-black text-gray-900 text-sm group-hover:text-indigo-700 transition-colors">{{ $s['career'] }}</p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $s['reason'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 ml-4">
                        <span class="text-xs font-black px-2.5 py-1 rounded-full
                            {{ $s['match'] >= 75 ? 'bg-emerald-100 text-emerald-700' : ($s['match'] >= 50 ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $s['match'] }}% match
                        </span>
                        <a href="{{ route('student.careers.explore', urlencode($s['career'])) }}"
                           class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black transition-all active:scale-95 whitespace-nowrap">
                            Explore →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── AI BLUEPRINT BUTTON ── --}}
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-3xl p-6 mb-6 flex items-center justify-between shadow-lg">
            <div class="text-white">
                <h2 class="font-bold text-lg flex items-center gap-2">✨ View Your AI Career Blueprint</h2>
                <p class="text-indigo-200 text-sm mt-0.5">See which career paths match your aptitude scores.</p>
            </div>
            <a href="{{ route('student.blueprint') }}"
               class="shrink-0 px-6 py-3 bg-white text-indigo-700 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-md text-sm whitespace-nowrap">
                Open Blueprint →
            </a>
        </div>

        {{-- ── REVIEW ANSWERS ── --}}
        <div class="bg-white rounded-3xl p-7 mb-6 border border-gray-100 shadow-sm" x-data="{ open: false }">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-black text-gray-900 text-xl flex items-center gap-2">👀 Review Answers</h2>
                <button @click="open = !open"
                        class="px-4 py-2 rounded-xl bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-200 text-sm font-bold text-gray-600 hover:text-indigo-600 transition-all flex items-center gap-2 active:scale-95">
                    <span x-text="open ? 'Hide' : 'Show all'"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>
            <p class="text-gray-400 text-sm font-medium mb-5">{{ $questions->count() }} questions answered · {{ $attempt->score }} correct · {{ $questions->count() - $attempt->score }} incorrect</p>

            <div x-show="open" x-transition style="display:none;" class="space-y-4">
                @foreach($questions as $index => $question)
                    @php
                        $userAnswer = $attempt->answers[(string) $question->_id] ?? null;
                        $isCorrect = $userAnswer === $question->correct_answer;
                    @endphp
                    <div class="rounded-2xl border p-5 {{ $isCorrect ? 'border-emerald-200 bg-emerald-50/30' : 'border-rose-200 bg-rose-50/30' }}">
                        <div class="flex gap-3 items-start mb-4">
                            <span class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-sm font-black
                                {{ $isCorrect ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $index + 1 }}
                            </span>
                            <p class="font-bold text-gray-900 leading-snug text-sm">{{ $question->question_text }}</p>
                        </div>
                        <div class="space-y-2 ml-11">
                            @foreach($question->options as $optIdx => $optText)
                                @php
                                    $isSelected = $userAnswer === $optText;
                                    $isRight    = $question->correct_answer === $optText;
                                    $cls = $isRight
                                        ? 'bg-emerald-100 border-emerald-300 text-emerald-900'
                                        : ($isSelected && !$isCorrect
                                            ? 'bg-rose-100 border-rose-300 text-rose-900'
                                            : 'bg-white border-gray-200 text-gray-600');
                                @endphp
                                <div class="flex items-center gap-2 p-3 rounded-xl border text-sm font-medium {{ $cls }} transition-colors">
                                    <span class="font-black opacity-50 shrink-0">{{ chr(65 + $optIdx) }}.</span>
                                    <span class="flex-1">{{ $optText }}</span>
                                    @if($isRight)
                                        <span class="text-[10px] font-black text-emerald-700 bg-emerald-200 px-2 py-0.5 rounded-full shrink-0">✓ Correct</span>
                                    @elseif($isSelected)
                                        <span class="text-[10px] font-black text-rose-700 bg-rose-200 px-2 py-0.5 rounded-full shrink-0">✗ Yours</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if(!empty($question->explanation))
                            <div class="ml-11 mt-3 p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-800 font-medium leading-relaxed">
                                <span class="font-black uppercase tracking-widest text-indigo-500 text-[9px] block mb-1">💡 Explanation</span>
                                {{ $question->explanation }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── NEXT STEPS ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('student.skill-gap.index') }}"
               class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all flex flex-col gap-3">
                <span class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">🧠</span>
                <div>
                    <p class="font-black text-gray-900 text-sm">Skill Gap Analysis</p>
                    <p class="text-gray-400 text-xs font-medium mt-0.5">Find what you're missing</p>
                </div>
            </a>
            <a href="{{ route('student.careers.index') }}"
               class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all flex flex-col gap-3">
                <span class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">🗺️</span>
                <div>
                    <p class="font-black text-gray-900 text-sm">Explore Careers</p>
                    <p class="text-gray-400 text-xs font-medium mt-0.5">Based on your strengths</p>
                </div>
            </a>
            <a href="{{ route('student.interview.index') }}"
               class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all flex flex-col gap-3">
                <span class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">🎤</span>
                <div>
                    <p class="font-black text-gray-900 text-sm">Mock Interview</p>
                    <p class="text-gray-400 text-xs font-medium mt-0.5">Practice for your dream role</p>
                </div>
            </a>
        </div>

    </div>
</x-app-layout>
