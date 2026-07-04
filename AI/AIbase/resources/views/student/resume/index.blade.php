<x-app-layout>
<div class="max-w-6xl mx-auto sm:px-6 lg:px-8 pb-12" x-data="resumePage()">

    {{-- ── Header ── --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-black uppercase tracking-widest mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Career Architect
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">AI Resume <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">Builder</span></h1>
            <p class="text-gray-500 mt-1 font-medium">Fill in your details, pick a career, and get a professional resume instantly.</p>
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('error'))
        <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl flex items-center gap-3">
            <span>⚠️</span><span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-5 py-4 rounded-2xl flex items-center gap-3">
            <span>✅</span><span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- ══════════════════════════════════════
             LEFT SIDEBAR – Generate Form
        ══════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-[2rem] p-7 border border-gray-100 shadow-sm">
                <h3 class="text-base font-black text-gray-900 mb-1">Build Your Resume</h3>
                <p class="text-xs text-gray-400 font-medium mb-5">Fill in your details and select a career path.</p>

                <form action="{{ route('student.resume.generate') }}" method="POST"
                      x-data="{ 
                          isGenerating: false, 
                          projectCount: 1, 
                          trainingCount: 1,
                          selectedCareer: '',
                          customCareer: '',
                          init() {
                              const existing = '{{ $resumeData['target_career'] ?? '' }}';
                              const standard = ['Full Stack Web Development', 'Data Scientist', 'AI Engineering', 'UI/UX', 'Software Engineering'];
                              if (existing) {
                                  if (standard.includes(existing)) {
                                      this.selectedCareer = existing;
                                  } else {
                                      this.selectedCareer = 'custom';
                                      this.customCareer = existing;
                                  }
                              }
                          }
                      }" @submit="isGenerating = true">
                    @csrf

                    {{-- Career --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Target Career *</label>
                        <select x-model="selectedCareer" required
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5 font-medium">
                            <option value="">Choose a path...</option>
                            <option value="Full Stack Web Development">Full Stack Web Development</option>
                            <option value="Data Scientist">Data Scientist</option>
                            <option value="AI Engineering">AI Engineering</option>
                            <option value="UI/UX">UI/UX</option>
                            <option value="Software Engineering">Software Engineering</option>
                            <option value="custom">Custom (Type your own...)</option>
                        </select>
                        <div x-show="selectedCareer === 'custom'" x-transition class="mt-3">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Enter Custom Career Path *</label>
                            <input type="text" x-model="customCareer" placeholder="e.g. Mobile Developer, Cloud Architect"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500"
                                x-bind:required="selectedCareer === 'custom'">
                        </div>
                        <input type="hidden" name="career_title" x-bind:value="selectedCareer === 'custom' ? customCareer : selectedCareer">
                    </div>

                    {{-- Phone --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="+91 9876543210"
                            value="{{ old('phone', $resumeData['phone'] ?? '') }}"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    {{-- LinkedIn --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-1">LinkedIn URL</label>
                        <input type="text" name="linkedin" placeholder="https://linkedin.com/in/yourname"
                            value="{{ old('linkedin', $resumeData['linkedin'] ?? '') }}"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    {{-- GitHub --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-1">GitHub URL</label>
                        <input type="text" name="github" placeholder="https://github.com/yourusername"
                            value="{{ old('github', $resumeData['github'] ?? '') }}"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    {{-- Education: 3 levels --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 mb-2">Education Details</label>

                        {{-- 10th --}}
                        <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">10th (Matriculation)</p>
                            <input type="text" name="school_10" placeholder="School Name"
                                value="{{ old('school_10', $resumeData['school_10'] ?? '') }}"
                                class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="marks_10" placeholder="Marks / % e.g. 99%"
                                    value="{{ old('marks_10', $resumeData['marks_10'] ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <input type="text" name="year_10" placeholder="Year e.g. Apr 2020 - Mar 2021"
                                    value="{{ old('year_10', $resumeData['year_10'] ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        {{-- Intermediate --}}
                        <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Intermediate (11th–12th)</p>
                            <input type="text" name="school_inter" placeholder="Junior College Name"
                                value="{{ old('school_inter', $resumeData['school_inter'] ?? '') }}"
                                class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="marks_inter" placeholder="Marks / % e.g. 96%"
                                    value="{{ old('marks_inter', $resumeData['marks_inter'] ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <input type="text" name="year_inter" placeholder="Year e.g. Apr 2021 - Mar 2023"
                                    value="{{ old('year_inter', $resumeData['year_inter'] ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        {{-- University --}}
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">University / Present</p>
                            <input type="text" name="college_name" placeholder="e.g. Lovely Professional University"
                                value="{{ old('college_name', $resumeData['college_name'] ?? ($user->school ?? '')) }}"
                                class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="marks" placeholder="CGPA e.g. 7.76"
                                    value="{{ old('marks', $resumeData['marks'] ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <input type="text" name="year_college" placeholder="e.g. Aug 2023 - Present"
                                    value="{{ old('year_college', $resumeData['year_college'] ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    {{-- Training --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-gray-600">Training / Internship</label>
                            <button type="button" @click="trainingCount++"
                                class="text-xs text-emerald-600 font-bold hover:underline">+ Add</button>
                        </div>
                        <template x-for="i in trainingCount" :key="i">
                            <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                <input type="text" :name="'training[' + (i-1) + '][title]'"
                                    placeholder="Training / Course Title"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <input type="text" :name="'training[' + (i-1) + '][provider]'"
                                    placeholder="Provider / Platform (e.g. SkillStone, Coursera)"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <input type="text" :name="'training[' + (i-1) + '][start_date]'"
                                        placeholder="Start: e.g. Jun 2025"
                                        class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <input type="text" :name="'training[' + (i-1) + '][end_date]'"
                                        placeholder="End: e.g. Jul 2025"
                                        class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <textarea :name="'training[' + (i-1) + '][description]'"
                                    placeholder="What you learned / achieved..."
                                    rows="2"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                            </div>
                        </template>
                    </div>

                    {{-- Projects --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-gray-600">Your Projects</label>
                            <button type="button" @click="projectCount++"
                                class="text-xs text-emerald-600 font-bold hover:underline">+ Add Project</button>
                        </div>
                        <template x-for="i in projectCount" :key="i">
                            <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                <input type="text" :name="'projects[' + (i-1) + '][title]'"
                                    placeholder="Project Title"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <textarea :name="'projects[' + (i-1) + '][description]'"
                                    placeholder="Brief description of what you built and its impact..."
                                    rows="2"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                            </div>
                        </template>
                    </div>

                    <button type="submit" :disabled="isGenerating"
                        class="w-full px-6 py-3.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold transition-all shadow-md active:scale-95 flex items-center justify-center gap-2 text-sm">
                        <span x-show="!isGenerating" class="flex items-center gap-2">
                            ✨ {{ $resumeData ? 'Regenerate Resume' : 'Generate Resume' }}
                        </span>
                        <span x-show="isGenerating" style="display:none" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            AI is writing...
                        </span>
                    </button>
                </form>
            </div>

            <div class="bg-indigo-50 rounded-[2rem] p-6 border border-indigo-100 shadow-sm">
                <h3 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-2">💡 AI Coach Tip</h3>
                <p class="text-xs text-indigo-700 font-medium leading-relaxed">
                    The AI uses your <strong>Aptitude Test scores</strong> + the details you fill in to craft a tailored resume. Add real projects for the best results!
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             RIGHT – Resume Preview / Edit
        ══════════════════════════════════════ --}}
        <div class="lg:col-span-3">
            @if($resumeData)

            {{-- Action Bar --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex gap-2">
                    <button @click="editMode = false"
                        :class="!editMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all">👁 Preview</button>
                    <button @click="editMode = true"
                        :class="editMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all">✏️ Edit</button>
                </div>
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-all shadow-sm">
                    🖨️ Print / PDF
                </button>
            </div>

            {{-- ── PREVIEW MODE ── --}}
            <div x-show="!editMode" id="resumePreview"
                 class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-8 font-serif" style="font-family: 'Times New Roman', Times, serif; color: #111;">

                    {{-- Name + Contact --}}
                    <div class="mb-3">
                        <h1 class="text-2xl font-black" style="color:#1a237e;">{{ $user->name }}</h1>
                        <div class="flex flex-wrap justify-between mt-1 gap-y-1">
                            {{-- Left: LinkedIn + GitHub --}}
                            <div class="text-xs space-y-0.5">
                                @if(!empty($resumeData['linkedin']))
                                <div><span class="font-bold">LinkedIn:</span>
                                    <a href="{{ $resumeData['linkedin'] }}" class="text-blue-700 hover:underline" target="_blank">{{ $resumeData['linkedin'] }}</a>
                                </div>
                                @endif
                                @if(!empty($resumeData['github']))
                                <div><span class="font-bold">GitHub:</span>
                                    <a href="{{ $resumeData['github'] }}" class="text-blue-700 hover:underline" target="_blank">{{ $resumeData['github'] }}</a>
                                </div>
                                @endif
                            </div>
                            {{-- Right: Email + Mobile --}}
                            <div class="text-xs text-right space-y-0.5">
                                <div><span class="font-bold">Email:</span>
                                    <a href="mailto:{{ $user->email }}" class="text-blue-700">{{ $user->email }}</a>
                                </div>
                                @if(!empty($resumeData['phone']))
                                <div><span class="font-bold">Mobile:</span> {{ $resumeData['phone'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SKILLS --}}
                    @php $skills = $resumeData['skills'] ?? []; @endphp
                    @if(!empty($skills))
                    <div class="mb-3">
                        <h2 class="text-xs font-black uppercase tracking-widest border-b border-gray-400 pb-0.5 mb-1" style="color:#1a237e; font-variant: small-caps; font-size:0.85rem;">Skills</h2>
                        <table class="w-full text-xs">
                            @if(!empty($skills['languages']))
                            <tr><td class="font-bold pr-3 py-0.5 w-32" style="color:#1a237e;">Languages:</td><td>{{ implode(', ', $skills['languages']) }}</td></tr>
                            @endif
                            @if(!empty($skills['frameworks']))
                            <tr><td class="font-bold pr-3 py-0.5" style="color:#1a237e;">Frameworks:</td><td>{{ implode(', ', $skills['frameworks']) }}</td></tr>
                            @endif
                            @if(!empty($skills['tools']))
                            <tr><td class="font-bold pr-3 py-0.5" style="color:#1a237e;">Tools/Platforms:</td><td>{{ implode(', ', $skills['tools']) }}</td></tr>
                            @endif
                            @if(!empty($skills['soft_skills']))
                            <tr><td class="font-bold pr-3 py-0.5" style="color:#1a237e;">Soft Skills:</td><td>{{ implode(', ', $skills['soft_skills']) }}</td></tr>
                            @endif
                        </table>
                    </div>
                    @endif

                    {{-- PROJECTS --}}
                    @if(!empty($resumeData['suggested_projects']))
                    <div class="mb-3">
                        <h2 class="text-xs font-black uppercase tracking-widest border-b border-gray-400 pb-0.5 mb-2" style="color:#1a237e; font-variant: small-caps; font-size:0.85rem;">Projects</h2>
                        @foreach($resumeData['suggested_projects'] as $project)
                        <div class="mb-3">
                            <div class="flex justify-between items-start">
                                <div class="text-xs font-bold" style="color:#1a237e;">
                                    {{ $project['title'] ?? '' }}
                                    @if(!empty($project['tech_stack'])) <span class="font-normal text-gray-700"> | {{ $project['tech_stack'] }}</span>@endif
                                    @if(!empty($project['github_url'])) <a href="{{ $project['github_url'] }}" class="text-blue-700 hover:underline ml-1" target="_blank">| GitHub</a>@endif
                                </div>
                                @if(!empty($project['duration']))<span class="text-xs text-gray-600 whitespace-nowrap ml-2">{{ $project['duration'] }}</span>@endif
                            </div>
                            @if(!empty($project['bullets']))
                                <ul class="list-disc pl-5 mt-0.5 space-y-0.5">
                                    @foreach($project['bullets'] as $b)
                                        <li class="text-xs text-gray-800">{{ $b }}</li>
                                    @endforeach
                                </ul>
                            @elseif(!empty($project['description']))
                                <p class="text-xs text-gray-700 mt-0.5">{{ $project['description'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- TRAINING --}}
                    @if(!empty($resumeData['training']))
                    <div class="mb-3">
                        <h2 class="text-xs font-black uppercase tracking-widest border-b border-gray-400 pb-0.5 mb-2" style="color:#1a237e; font-variant: small-caps; font-size:0.85rem;">Training</h2>
                        @foreach($resumeData['training'] as $t)
                        <div class="mb-2">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold" style="color:#1a237e;">{{ $t['title'] ?? '' }}
                                    @if(!empty($t['provider'])) <span class="font-normal text-gray-700">| {{ $t['provider'] }}</span>@endif
                                </span>
                                @if(!empty($t['duration']))<span class="text-xs text-gray-600">{{ $t['duration'] }}</span>@endif
                            </div>
                            @if(!empty($t['bullets']))
                            <ul class="list-disc pl-5 mt-0.5 space-y-0.5">
                                @foreach($t['bullets'] as $b)<li class="text-xs text-gray-800">{{ $b }}</li>@endforeach
                            </ul>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- CERTIFICATES --}}
                    @if(!empty($resumeData['certificates']))
                    <div class="mb-3">
                        <h2 class="text-xs font-black uppercase tracking-widest border-b border-gray-400 pb-0.5 mb-1" style="color:#1a237e; font-variant: small-caps; font-size:0.85rem;">Certificates</h2>
                        @foreach($resumeData['certificates'] as $cert)
                        <div class="flex justify-between text-xs py-0.5">
                            <span>• {{ $cert['title'] ?? '' }}
                                @if(!empty($cert['provider'])) | <a href="{{ $cert['url'] ?? '#' }}" class="text-blue-700 hover:underline" target="_blank">{{ $cert['provider'] }}</a>@endif
                            </span>
                            @if(!empty($cert['date']))<span class="text-gray-600">{{ $cert['date'] }}</span>@endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- ACHIEVEMENTS --}}
                    @if(!empty($resumeData['achievements']))
                    <div class="mb-3">
                        <h2 class="text-xs font-black uppercase tracking-widest border-b border-gray-400 pb-0.5 mb-1" style="color:#1a237e; font-variant: small-caps; font-size:0.85rem;">Achievements</h2>
                        @foreach($resumeData['achievements'] as $ach)
                        <div class="mb-1">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold" style="color:#1a237e;">{{ $ach['title'] ?? '' }}</span>
                                @if(!empty($ach['date']))<span class="text-xs text-gray-600">{{ $ach['date'] }}</span>@endif
                            </div>
                            @if(!empty($ach['description']))<p class="text-xs text-gray-700 pl-3">• {{ $ach['description'] }}</p>@endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- EDUCATION --}}
                    @if(!empty($resumeData['education']))
                    <div class="mb-2">
                        <h2 class="text-xs font-black uppercase tracking-widest border-b border-gray-400 pb-0.5 mb-1" style="color:#1a237e; font-variant: small-caps; font-size:0.85rem;">Education</h2>
                        @foreach($resumeData['education'] as $edu)
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="text-xs font-bold" style="color:#1a237e;">{{ $edu['institution'] ?? '' }}</p>
                                <p class="text-xs text-gray-700">{{ $edu['degree'] ?? '' }}
                                    @if(!empty($edu['score'])) <span class="font-bold">; {{ str_contains(strtolower($edu['score']), 'cgpa') || str_contains($edu['score'], '.') ? 'CGPA' : 'Percentage' }}: {{ $edu['score'] }}</span>@endif
                                </p>
                            </div>
                            <div class="text-right text-xs text-gray-600 whitespace-nowrap ml-4">
                                @if(!empty($edu['location']))<p>{{ $edu['location'] }}</p>@endif
                                @if(!empty($edu['duration']))<p>{{ $edu['duration'] }}</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-gray-200 text-center">
                        <p class="text-[9px] text-gray-400 uppercase tracking-widest">Generated by RiseStar AI · {{ \Carbon\Carbon::parse($resumeData['generated_at'] ?? now())->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- ── EDIT MODE ── --}}
            <div x-show="editMode" style="display:none">
                <form action="{{ route('student.resume.update') }}" method="POST" class="bg-white rounded-[2rem] p-7 border border-gray-100 shadow-sm space-y-5">
                    @csrf
                    <h3 class="text-base font-black text-gray-900">Edit Resume Details</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ $resumeData['phone'] ?? '' }}"
                                class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Marks / CGPA</label>
                            <input type="text" name="marks" value="{{ $resumeData['marks'] ?? '' }}"
                                class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">LinkedIn URL</label>
                        <input type="text" name="linkedin" value="{{ $resumeData['linkedin'] ?? '' }}"
                            class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">GitHub URL</label>
                        <input type="text" name="github" value="{{ $resumeData['github'] ?? '' }}"
                            class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">College / School</label>
                        <input type="text" name="college_name" value="{{ $resumeData['college_name'] ?? '' }}"
                            class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Professional Summary</label>
                        <textarea name="professional_summary" rows="3"
                            class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500 resize-none">{{ $resumeData['professional_summary'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Skills (comma-separated)</label>
                        <input type="text" name="core_competencies"
                            value="{{ implode(', ', $resumeData['core_competencies'] ?? []) }}"
                            class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    {{-- Edit Projects --}}
                    <div x-data="{ projects: {{ json_encode(array_values($resumeData['suggested_projects'] ?? [])) }} }">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-gray-600">Projects</label>
                            <button type="button" @click="projects.push({title:'',description:''})"
                                class="text-xs text-emerald-600 font-bold hover:underline">+ Add</button>
                        </div>
                        <template x-for="(proj, idx) in projects" :key="idx">
                            <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200 relative">
                                <button type="button" @click="projects.splice(idx,1)"
                                    class="absolute top-2 right-2 text-rose-400 hover:text-rose-600 text-xs font-bold">✕</button>
                                <input type="text" :name="'projects[' + idx + '][title]'" :value="proj.title"
                                    placeholder="Project Title"
                                    class="w-full bg-white border border-gray-200 text-xs rounded-lg p-2 mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <textarea :name="'projects[' + idx + '][description]'" :value="proj.description"
                                    placeholder="Description" rows="2"
                                    class="w-full bg-white border border-gray-200 text-xs rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                            </div>
                        </template>
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all shadow-md">
                        💾 Save Changes
                    </button>
                </form>
            </div>

            @else
            <div class="bg-white rounded-[2rem] p-12 border border-gray-100 shadow-sm text-center flex flex-col items-center justify-center min-h-[400px]">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-5 shadow-inner">📄</div>
                <h2 class="text-xl font-black text-gray-900">Ready to build your resume?</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xs">Fill in your details on the left and click Generate Resume.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #resumePreview, #resumePreview * { visibility: visible; }
    #resumePreview { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; border-radius: 0 !important; }
    .no-print { display: none !important; }
}
</style>

<script>
function resumePage() {
    return { editMode: false };
}
</script>
</x-app-layout>