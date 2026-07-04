<x-app-layout>
    <div x-data="{
            activeRealm: {{ isset($autoExpand) && $autoExpand ? "'" . ($autoExpand['category'] ?? '') . "'" : 'null' }},
            activeCareer: {{ isset($autoExpand) && $autoExpand ? "'" . ($autoExpand['id'] ?? '') . "'" : 'null' }},
            careers: {{ Js::from($careers->map(fn($c) => array_merge($c->toArray(), ['_sid' => (string)$c->_id]))) }},
            toggleRealm(key) { this.activeRealm = this.activeRealm === key ? null : key; this.activeCareer = null; },
            toggleCareer(id) { this.activeCareer = this.activeCareer === id ? null : id; },
            getRealmCareers(key) { return this.careers.filter(c => c.category === key); }
         }" class="relative min-h-screen py-12 max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100 mb-5 shadow-sm">
                🎓 Explore Career Pathways
            </div>
            <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tight mb-3 leading-none">
                Career <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-cyan-500">Library</span>
            </h1>
            <p class="text-gray-500 text-base font-medium max-w-lg mx-auto">Discover → Learn → Roadmap → Placement. Everything you need for your career journey.</p>
        </div>

        <div class="space-y-5">
            @php
                $realms = [
                    ['key'=>'Tech',        'title'=>'Technology Core',       'icon'=>'💻','color'=>'indigo', 'number'=>'01'],
                    ['key'=>'Business',    'title'=>'Global Commerce',       'icon'=>'📈','color'=>'emerald','number'=>'02'],
                    ['key'=>'Bio-Sciences','title'=>'Bio-Sciences',          'icon'=>'🧬','color'=>'rose',   'number'=>'03'],
                    ['key'=>'Arts',        'title'=>'Creative Arts',         'icon'=>'🎨','color'=>'amber',  'number'=>'04'],
                    ['key'=>'Healthcare',  'title'=>'Healthcare',            'icon'=>'🏥','color'=>'teal',   'number'=>'05'],
                    ['key'=>'Engineering', 'title'=>'Engineering',           'icon'=>'⚙️','color'=>'blue',   'number'=>'06'],
                    ['key'=>'Law',         'title'=>'Law & Public Services', 'icon'=>'⚖️','color'=>'purple', 'number'=>'07'],
                    ['key'=>'Education',   'title'=>'Education',             'icon'=>'🎓','color'=>'cyan',   'number'=>'08'],
                    ['key'=>'Media',       'title'=>'Media & Communication', 'icon'=>'📡','color'=>'pink',   'number'=>'09'],
                    ['key'=>'Finance',     'title'=>'Finance & Banking',     'icon'=>'🏦','color'=>'green',  'number'=>'10'],
                ];
            @endphp

            @foreach($realms as $realm)
            <div class="rounded-[2rem] border bg-white shadow-sm transition-all duration-300"
                 :class="activeRealm === '{{ $realm['key'] }}' ? 'border-indigo-200 ring-4 ring-indigo-50/50 shadow-md' : 'border-gray-200 hover:border-indigo-300 hover:shadow-md'">

                {{-- Realm Header --}}
                <button @click="toggleRealm('{{ $realm['key'] }}')" class="w-full px-8 py-7 flex items-center justify-between text-left group">
                    <div class="flex items-center gap-5">
                        <span class="text-4xl opacity-10 font-black italic text-gray-900 group-hover:opacity-20 transition-opacity">{{ $realm['number'] }}</span>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-{{ $realm['color'] }}-50 border border-white shadow-md group-hover:scale-110 transition-transform">
                            {{ $realm['icon'] }}
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $realm['title'] }}</h3>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-0.5">View Careers</p>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-full border flex items-center justify-center transition-all duration-300 shadow-sm"
                         :class="activeRealm === '{{ $realm['key'] }}' ? 'rotate-180 bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-hover:border-indigo-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </button>

                {{-- Career List --}}
                <div x-show="activeRealm === '{{ $realm['key'] }}'" x-transition x-cloak class="border-t border-gray-100 bg-gray-50/40">
                    <div class="p-6">

                        <template x-if="getRealmCareers('{{ $realm['key'] }}').length === 0">
                            <div class="py-10 text-center text-gray-400 font-medium text-sm bg-white rounded-2xl border border-dashed border-gray-200">
                                No career paths listed in this category yet.
                            </div>
                        </template>

                        <div class="space-y-4">
                            <template x-for="career in getRealmCareers('{{ $realm['key'] }}')" :key="career._sid">
                                <div class="rounded-2xl border bg-white overflow-hidden transition-all shadow-sm"
                                     :class="activeCareer === career._sid ? 'border-indigo-200 ring-2 ring-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:shadow-md'">

                                    {{-- Career Trigger --}}
                                    <button @click="toggleCareer(career._sid)" class="w-full flex items-center justify-between px-6 py-5 text-left group/btn">
                                        <div class="flex items-center gap-4">
                                            <div class="w-2.5 h-2.5 rounded-full transition-colors"
                                                 :class="activeCareer === career._sid ? 'bg-indigo-500 animate-pulse ring-4 ring-indigo-100' : 'bg-gray-300'"></div>
                                            <div>
                                                <h4 class="text-base font-bold tracking-tight transition-colors"
                                                    :class="activeCareer === career._sid ? 'text-indigo-700' : 'text-gray-900 group-hover/btn:text-indigo-600'"
                                                    x-text="career.title"></h4>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" x-text="career.salary_range || 'Competitive'"></span>
                                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full"
                                                          :class="{
                                                            'bg-green-100 text-green-700': career.demand_level === 'High',
                                                            'bg-yellow-100 text-yellow-700': career.demand_level === 'Medium',
                                                            'bg-red-100 text-red-700': career.demand_level === 'Low'
                                                          }"
                                                          x-text="(career.demand_level || '') + ' Demand'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-all"
                                              :class="activeCareer === career._sid ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-600 border-gray-200 group-hover/btn:bg-indigo-50 group-hover/btn:text-indigo-700'">
                                            <span x-text="activeCareer === career._sid ? 'Close' : 'Explore'"></span>
                                        </span>
                                    </button>

                                    {{-- Full Career Detail --}}
                                    <div x-show="activeCareer === career._sid" x-transition x-cloak>
                                        <div class="px-6 pb-6">
                                            <div class="space-y-4">

                                                {{-- Row 1: Overview + Salary --}}
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div class="md:col-span-2 bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
                                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Career Overview</p>
                                                        <p class="text-sm text-indigo-900 font-semibold leading-relaxed" x-text="career.overview || career.description"></p>
                                                    </div>
                                                    <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                                                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3">💰 Salary Range</p>
                                                        <div class="space-y-1.5 text-xs font-bold">
                                                            <div class="flex justify-between"><span class="text-gray-500">Fresher</span><span class="text-emerald-700" x-text="career.salary_fresher || '—'"></span></div>
                                                            <div class="flex justify-between"><span class="text-gray-500">Mid-level</span><span class="text-emerald-700" x-text="career.salary_mid || '—'"></span></div>
                                                            <div class="flex justify-between"><span class="text-gray-500">Senior</span><span class="text-emerald-700" x-text="career.salary_senior || '—'"></span></div>
                                                        </div>
                                                        <div class="mt-3 pt-3 border-t border-emerald-200">
                                                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Demand</p>
                                                            <span class="text-xs font-black px-2 py-1 rounded-full"
                                                                  :class="{
                                                                    'bg-green-200 text-green-800': career.demand_level === 'High',
                                                                    'bg-yellow-200 text-yellow-800': career.demand_level === 'Medium',
                                                                    'bg-red-200 text-red-800': career.demand_level === 'Low'
                                                                  }"
                                                                  x-text="career.demand_level"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 2: Skills + Languages --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3">🎯 Required Skills</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <template x-for="skill in (career.required_skills || [])" :key="skill">
                                                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100" x-text="skill"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3">💻 Languages Known</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <template x-for="lang in (career.languages_known || [])" :key="lang">
                                                                <span class="px-3 py-1 bg-cyan-50 text-cyan-700 rounded-lg text-xs font-bold border border-cyan-100" x-text="lang"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 3: Eligibility + Tools --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-violet-600 uppercase tracking-widest mb-3">🎓 Eligibility / Education</p>
                                                        <ul class="space-y-1.5">
                                                            <template x-for="e in (career.eligibility || [])" :key="e">
                                                                <li class="text-xs font-semibold text-gray-700 flex items-start gap-2">
                                                                    <span class="text-violet-400 mt-0.5">✓</span><span x-text="e"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-3">🛠️ Tools & Technologies</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <template x-for="tool in (career.tools || [])" :key="tool">
                                                                <span class="px-2.5 py-1 bg-orange-50 text-orange-700 rounded-lg text-xs font-bold border border-orange-100" x-text="tool"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 4: Roadmap (full width) --}}
                                                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden">
                                                    <div class="absolute inset-y-0 left-[35px] w-[2px] bg-gray-100 top-16"></div>
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-5 border-b border-gray-100 pb-2">🗺️ Step-by-Step Learning Roadmap</p>
                                                    <div class="space-y-4 relative z-10">
                                                        <template x-for="(step, idx) in (career.roadmap_steps || [])" :key="idx">
                                                            <div x-data="{ loading: false, details: null, open: false }">
                                                                <button @click="if(!details) { loading = true; fetch('{{ route('student.careers.explain-step') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({step: step}) }).then(r => r.json()).then(d => { details = d.explanation; loading = false; open = true; }).catch(() => loading = false); } else { open = !open; }"
                                                                    class="flex items-start gap-4 text-left w-full hover:bg-gray-50 p-2 -mx-2 rounded-xl transition-all group">
                                                                    <div class="w-7 h-7 rounded-full bg-white border-2 flex items-center justify-center flex-shrink-0 relative z-20 mt-0.5 shadow-sm transition-all"
                                                                         :class="open ? 'border-indigo-500 ring-4 ring-indigo-50' : 'border-gray-300 group-hover:border-indigo-400'">
                                                                        <span class="text-[10px] font-black text-gray-500" :class="open ? 'text-indigo-600' : ''" x-text="idx + 1"></span>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <p class="text-sm font-bold leading-relaxed transition-colors"
                                                                           :class="open ? 'text-indigo-700' : 'text-gray-700 group-hover:text-gray-900'" x-text="step"></p>
                                                                        <p x-show="loading" class="text-[10px] text-indigo-500 mt-1 animate-pulse font-black uppercase tracking-widest">Loading AI details...</p>
                                                                        <div x-show="open && details" x-transition
                                                                             class="mt-3 text-xs text-gray-600 leading-relaxed bg-indigo-50/60 p-4 rounded-xl border border-indigo-100 font-medium" x-text="details"></div>
                                                                    </div>
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Row 5: Projects + Certifications --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-3">🚀 Projects to Build</p>
                                                        <ul class="space-y-1.5">
                                                            <template x-for="proj in (career.projects_to_build || [])" :key="proj">
                                                                <li class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                                                    <span class="text-teal-400">▸</span><span x-text="proj"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-3">🏅 Certifications</p>
                                                        <ul class="space-y-1.5">
                                                            <template x-for="cert in (career.certifications || [])" :key="cert">
                                                                <li class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                                                    <span class="text-yellow-400">★</span><span x-text="cert"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                </div>

                                                {{-- Row 6: Career Growth + Companies --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest mb-3">📈 Career Growth Path</p>
                                                        <div class="flex flex-wrap items-center gap-1">
                                                            <template x-for="(step, idx) in (career.career_growth || [])" :key="step">
                                                                <div class="flex items-center gap-1">
                                                                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100" x-text="step"></span>
                                                                    <span x-show="idx < (career.career_growth.length - 1)" class="text-gray-300 text-xs font-black">→</span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3">🏢 Companies Hiring</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <template x-for="co in (career.companies_hiring || [])" :key="co">
                                                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100" x-text="co"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 7: Related Careers + Future Scope --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">🔗 Related Careers</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <template x-for="rel in (career.related_careers || [])" :key="rel">
                                                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold border border-gray-200" x-text="rel"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="bg-gradient-to-br from-indigo-50 to-cyan-50 rounded-2xl p-5 border border-indigo-100">
                                                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-2">🔮 Future Scope</p>
                                                        <p class="text-xs text-indigo-800 font-semibold leading-relaxed" x-text="career.future_scope"></p>
                                                    </div>
                                                </div>

                                                {{-- Row 8: Job Roles + Colleges --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">💼 Popular Job Roles</p>
                                                        <ul class="space-y-1.5">
                                                            <template x-for="role in (career.job_roles || [])" :key="role">
                                                                <li class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                                                    <span class="text-indigo-400">❖</span><span x-text="role"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">🎓 Top Colleges</p>
                                                        <ul class="space-y-1.5">
                                                            <template x-for="college in (career.colleges || [])" :key="college">
                                                                <li class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                                                    <span class="text-emerald-500">🎓</span><span x-text="college"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-app-layout>

