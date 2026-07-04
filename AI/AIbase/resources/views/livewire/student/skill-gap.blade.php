<div>
    <div class="bg-white/60 backdrop-blur-xl rounded-[2.5rem] p-8 md:p-12 mb-10 border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
        <!-- Background Gradient -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-gradient-to-br from-cyan-400/20 to-blue-500/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-3">Skill-Gap Analysis & Roadmap</h2>
            <p class="text-gray-500 font-medium text-lg mb-8">Select your target career path. Our AI will analyze your aptitude test history, identify your missing skills, and generate a customized learning roadmap to get you hired.</p>

            @if($error)
                <div class="mb-6 bg-rose-50/80 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-start gap-3 backdrop-blur-sm">
                    <span class="text-xl">⚠️</span>
                    <span class="font-bold text-sm">{{ $error }}</span>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="text" wire:model.defer="selectedCareer" list="career-options" placeholder="E.g., Software Engineer, Marketing Manager..." class="w-full bg-white/80 backdrop-blur-sm border-gray-200 text-gray-900 text-base rounded-2xl focus:ring-cyan-500 focus:border-cyan-500 block p-4 font-bold shadow-sm transition-all hover:border-cyan-200 placeholder-gray-400">
                    <datalist id="career-options">
                        @foreach($careers as $career)
                            <option value="{{ $career->title }}">
                        @endforeach
                    </datalist>
                </div>

                <button wire:click="generateAnalysis" wire:loading.attr="disabled" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold transition-all shadow-xl shadow-cyan-500/20 active:scale-95 disabled:opacity-70 group/btn shrink-0">
                    <span wire:loading.remove wire:target="generateAnalysis" class="flex items-center">
                        <span class="mr-2 text-xl group-hover/btn:rotate-12 transition-transform">⚡</span> Analyze Gap
                    </span>
                    <span wire:loading wire:target="generateAnalysis" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Computing...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Loading State Skeleton -->
    <div wire:loading wire:target="generateAnalysis" class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white/40 rounded-3xl p-6 h-32 animate-pulse border border-white/50"></div>
            <div class="bg-white/40 rounded-3xl p-6 h-32 animate-pulse border border-white/50 md:col-span-2"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white/40 rounded-[2rem] p-8 h-96 animate-pulse border border-white/50"></div>
            <div class="bg-white/40 rounded-[2rem] p-8 h-96 animate-pulse border border-white/50"></div>
        </div>
    </div>

    @if($analysisData && !$isAnalyzing)
        <!-- Analysis Results Dashboard -->
        <div class="space-y-8 animate-[fadeIn_0.5s_ease-out]">
            
            <div class="flex items-center gap-4 mb-4">
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest px-4">Analysis Report for {{ $selectedCareer }}</span>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            <!-- Top Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Current Readiness Level -->
                <div class="bg-white/60 backdrop-blur-xl rounded-3xl p-6 border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-100 to-transparent rounded-bl-full pointer-events-none"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Current Level</span>
                    
                    @php
                        $levelStr = strtolower($analysisData['current_level'] ?? 'Beginner');
                        $colorClass = str_contains($levelStr, 'advanced') ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : (str_contains($levelStr, 'intermediate') ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-rose-600 bg-rose-50 border-rose-100');
                    @endphp
                    
                    <div class="inline-flex px-4 py-2 rounded-xl border {{ $colorClass }} font-black text-xl mb-3 shadow-sm">
                        {{ $analysisData['current_level'] ?? 'Beginner' }}
                    </div>
                    <p class="text-sm font-medium text-gray-500">Based on your aptitude tests.</p>
                </div>

                <!-- Overall Advice -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-6 border border-gray-700 shadow-xl md:col-span-2 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:bg-cyan-500/10 transition-colors pointer-events-none"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span> AI Conclusion
                    </span>
                    <p class="text-white font-medium text-base leading-relaxed">
                        {{ $analysisData['overall_advice'] ?? 'Keep learning and building your skills!' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Missing Skills -->
                <div class="bg-white/60 backdrop-blur-xl rounded-[2rem] p-8 border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-all">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3 mb-6">
                        <span class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg">⚠️</span>
                        Missing Competencies
                    </h3>
                    
                    <ul class="space-y-4">
                        @foreach($analysisData['missing_skills'] ?? [] as $skill)
                            <li class="flex items-start gap-3 p-4 rounded-2xl bg-white border border-gray-50 shadow-sm hover:border-rose-100 transition-colors">
                                <span class="text-rose-500 mt-0.5 font-bold">×</span>
                                <span class="font-bold text-gray-700 text-sm">{{ $skill }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Learning Resources -->
                <div class="bg-white/60 backdrop-blur-xl rounded-[2rem] p-8 border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-all">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-3 mb-6">
                        <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">📚</span>
                        Recommended Resources
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($analysisData['learning_resources'] ?? [] as $resource)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-50 shadow-sm hover:border-emerald-100 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                        @if(($resource['type'] ?? '') == 'Course') 🎓
                                        @elseif(($resource['type'] ?? '') == 'Book') 📖
                                        @elseif(($resource['type'] ?? '') == 'Video') 📺
                                        @else 🌍
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $resource['name'] ?? '' }}</h4>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ $resource['type'] ?? 'Resource' }}</p>
                                    </div>
                                </div>
                                <a href="https://www.google.com/search?q={{ urlencode(($resource['name'] ?? '') . ' course') }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 bg-emerald-50 p-2 rounded-xl transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actionable Roadmap -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-[2rem] p-8 border border-blue-500/50 shadow-2xl shadow-blue-500/20 relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30"></div>
                
                <h3 class="text-xl font-black text-white flex items-center gap-3 mb-8 relative z-10">
                    <span class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg backdrop-blur-md">🗺️</span>
                    Your Action Plan
                </h3>

                <div class="relative z-10 space-y-6">
                    @foreach($analysisData['recommended_actions'] ?? [] as $index => $action)
                        <div class="flex gap-6">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full bg-white text-blue-600 font-black flex items-center justify-center shadow-lg shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                @if(!$loop->last)
                                    <div class="w-1 h-full bg-white/20 mt-2 rounded-full"></div>
                                @endif
                            </div>
                            <div class="pb-6">
                                <h4 class="font-black text-lg text-white mb-2">{{ $action['title'] ?? '' }}</h4>
                                <p class="text-blue-100 font-medium text-sm leading-relaxed max-w-2xl">{{ $action['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    @endif
</div>
