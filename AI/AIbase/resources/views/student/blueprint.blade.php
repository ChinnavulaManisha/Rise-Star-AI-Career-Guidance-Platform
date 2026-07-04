<x-app-layout>
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 pb-16">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pt-2">
        <div>
            <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-1">AI-Powered Analysis</p>
            <h1 class="text-3xl font-bold text-gray-900">Your Career <span class="text-indigo-600">Blueprint</span></h1>
            <p class="text-gray-500 text-sm mt-1">Based on your aptitude test � here are the careers that fit you best.</p>
        </div>
        <form action="{{ route('student.blueprint.regenerate') }}" method="POST" x-data="{loading:false}" @submit="loading=true">
            @csrf
            <button type="submit" :disabled="loading"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-all shadow-sm">
                <span x-show="!loading">? Regenerate</span>
                <span x-show="loading" style="display:none" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Analyzing...
                </span>
            </button>
        </form>
    </div>

    @if(session('error'))
        <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium">?? {{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">? {{ session('success') }}</div>
    @endif

    @if(!$blueprint)
    <div class="bg-white rounded-2xl p-12 border border-gray-100 shadow-sm text-center">
        <div class="text-5xl mb-4">??</div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Take an Aptitude Test First</h2>
        <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">Complete at least one aptitude test to unlock your personalized career blueprint.</p>
        <a href="{{ route('student.tests.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-all">
            ?? Take Aptitude Test
        </a>
    </div>
    @else

    @if(!($blueprint['has_real_scores'] ?? true))
    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
        ?? Your last test didn't record category scores. Retake the General Aptitude Assessment for accurate results.
        <a href="{{ route('student.tests.index') }}" class="underline font-bold ml-1">Retake now ?</a>
    </div>
    @endif

    {{-- Score Cards --}}
    <div class="grid grid-cols-5 gap-3 mb-6">
        @php
            $scoreCards = [
                ['label'=>'Overall',    'val'=>$blueprint['overall'],     'color'=>'indigo'],
                ['label'=>'Logical',    'val'=>$blueprint['logical'],     'color'=>'blue'],
                ['label'=>'Numerical',  'val'=>$blueprint['numerical'],   'color'=>'emerald'],
                ['label'=>'Verbal',     'val'=>$blueprint['verbal'],      'color'=>'violet'],
                ['label'=>'Personality','val'=>$blueprint['personality'], 'color'=>'rose'],
            ];
        @endphp
        @foreach($scoreCards as $sc)
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ $sc['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $sc['val'] }}<span class="text-sm text-gray-400">%</span></p>
            <div class="mt-2 w-full bg-gray-100 rounded-full h-1">
                <div class="h-1 rounded-full bg-{{ $sc['color'] }}-500" style="width:{{ $sc['val'] }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Interest Domain (if available) --}}
    @if(!empty($blueprint['interest_domain']) && array_sum($blueprint['interest_domain']) > 0)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-1">🎯 Career Interest Profile</h2>
        <p class="text-xs text-gray-400 mb-4">
            @if(!empty($blueprint['focus_topic']))
                Based on your Focus Topic: <strong class="text-indigo-600 font-bold">{{ ucfirst($blueprint['focus_topic']) }}</strong>
            @else
                Based on your Career Interest Discovery Test
            @endif
        </p>
        <div class="grid grid-cols-4 gap-3">
            @php
                $iColors = ['Tech'=>'indigo','Healthcare'=>'rose','Business'=>'emerald','Arts'=>'amber'];
                $iIcons  = ['Tech'=>'💻','Healthcare'=>'🏥','Business'=>'📈','Arts'=>'🎨'];
            @endphp
            @foreach($blueprint['interest_domain'] as $domain => $pct)
            <div class="text-center p-3 rounded-xl border {{ $pct === max($blueprint['interest_domain']) ? 'border-indigo-200 bg-indigo-50' : 'border-gray-100 bg-gray-50' }}">
                <p class="text-xl mb-1">{{ $iIcons[$domain] ?? '🎯' }}</p>
                <p class="text-xs font-bold text-gray-700">{{ $domain }}</p>
                <p class="text-lg font-bold {{ $pct === max($blueprint['interest_domain']) ? 'text-indigo-600' : 'text-gray-500' }}">{{ $pct }}%</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- AI Insight --}}
    @if(!empty($blueprint['ai_insight']))
    <div class="bg-indigo-600 rounded-2xl p-6 mb-6 text-white">
        <p class="text-xs font-semibold text-indigo-200 uppercase tracking-widest mb-1">? AI Insight</p>
        <p class="text-sm leading-relaxed text-white/90">{{ $blueprint['ai_insight'] }}</p>
    </div>
    @else
    <div class="bg-indigo-600 rounded-2xl p-6 mb-6 text-white">
        <p class="text-xs font-semibold text-indigo-200 uppercase tracking-widest mb-1">Your Strongest Domain</p>
        <p class="text-lg font-bold">{{ $blueprint['strongest_area'] }}</p>
        <p class="text-sm text-white/80 mt-1">This is where your aptitude scores point most strongly.</p>
    </div>
    @endif

    {{-- Domain Scores --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">Domain Aptitude Match</h2>
        <div class="space-y-3">
            @foreach($blueprint['area_scores'] as $area => $score)
            @php $isTop = $area === $blueprint['strongest_area']; @endphp
            <div class="flex items-center gap-4">
                <div class="w-44 shrink-0 flex items-center gap-2">
                    @if($isTop)<span class="text-[9px] bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-bold uppercase">Best</span>@endif
                    <span class="text-sm {{ $isTop ? 'font-bold text-indigo-700' : 'font-medium text-gray-600' }} truncate">{{ $area }}</span>
                </div>
                <div class="flex-1 bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $isTop ? 'bg-indigo-500' : 'bg-gray-300' }}" style="width:{{ $score }}%"></div>
                </div>
                <span class="text-sm font-semibold {{ $isTop ? 'text-indigo-600' : 'text-gray-400' }} w-10 text-right">{{ $score }}%</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Career Recommendations --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-gray-900">?? Recommended Career Paths</h2>
                <p class="text-xs text-gray-400 mt-0.5">Matched to your aptitude scores. Click Explore to see full details.</p>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($blueprint['top_careers'] as $i => $career)
            @php
                $score = $career['score'];
                $rank = $i + 1;
                $barColor = $score >= 70 ? 'bg-emerald-500' : ($score >= 50 ? 'bg-indigo-500' : 'bg-amber-400');
                $textColor = $score >= 70 ? 'text-emerald-700' : ($score >= 50 ? 'text-indigo-700' : 'text-amber-700');
                $bgColor = $score >= 70 ? 'bg-emerald-50' : ($score >= 50 ? 'bg-indigo-50' : 'bg-amber-50');
            @endphp
            <div class="flex items-center gap-4 p-4 rounded-xl border {{ $rank === 1 ? 'border-indigo-200 bg-indigo-50/50' : 'border-gray-100 bg-gray-50' }} hover:border-indigo-300 transition-all group">

                {{-- Rank --}}
                <div class="w-8 h-8 rounded-lg {{ $rank === 1 ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-500' }} flex items-center justify-center text-xs font-bold shrink-0">
                    {{ $rank === 1 ? '?' : $rank }}
                </div>

                {{-- Icon --}}
                <span class="text-xl shrink-0">{{ $career['icon'] }}</span>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm group-hover:text-indigo-700 transition-colors">{{ $career['title'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $career['reason'] }}</p>
                </div>

                {{-- Score bar --}}
                <div class="hidden sm:flex items-center gap-2 w-28 shrink-0">
                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                        <div class="{{ $barColor }} h-1.5 rounded-full" style="width:{{ $score }}%"></div>
                    </div>
                    <span class="text-xs font-bold {{ $textColor }} w-8 text-right">{{ $score }}%</span>
                </div>

                {{-- Explore button --}}
                <a href="{{ route('student.careers.explore', urlencode($career['title'])) }}"
                   class="px-3 py-2 rounded-lg bg-gray-900 hover:bg-indigo-600 text-white text-xs font-semibold transition-all shrink-0">
                    Explore →
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">Test taken {{ \Carbon\Carbon::parse($blueprint['attempt_date'])->diffForHumans() }}</p>
            <a href="{{ route('student.careers.index') }}"
               class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                Browse all careers →
            </a>
        </div>
    </div>

    @endif
</div>
</x-app-layout>

