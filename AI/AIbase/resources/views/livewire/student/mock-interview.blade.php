<div x-data="{}" class="min-h-[calc(100vh-8rem)] flex flex-col">

    {{-- ══ SETUP STATE ══ --}}
    @if($status === 'setup')
    <div class="flex-1 flex flex-col items-center justify-center px-4 py-10">

        {{-- Hero --}}
        <div class="text-center mb-10 max-w-lg">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#1E1B4B] to-[#4F46E5] flex items-center justify-center text-4xl shadow-2xl shadow-indigo-500/30 mx-auto mb-6">🎤</div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">AI Mock Interview</h1>
            <p class="text-gray-500 font-medium text-base">Select your target role and our AI Hiring Manager will ask you <strong class="text-gray-900">{{ $maxQuestions }} real interview questions</strong> with personalized feedback at the end.</p>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
            <label class="block text-sm font-black text-gray-700 mb-2 uppercase tracking-widest text-[11px]">Target Career Role</label>
            <select wire:model.live="selectedCareer"
                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl p-3.5 font-semibold mb-4 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                <option value="">Choose a career path...</option>
                <option value="Full Stack Web Development">Full Stack Web Development</option>
                <option value="Data Scientist">Data Scientist</option>
                <option value="AI Engineering">AI Engineering</option>
                <option value="UI/UX">UI/UX</option>
                <option value="Software Engineering">Software Engineering</option>
                <option value="custom">Custom (Type your own...)</option>
            </select>

            @if($selectedCareer === 'custom')
                <div class="mb-5">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest text-[11px] mb-2">Enter Custom Career Path *</label>
                    <input type="text" wire:model.defer="customCareer" placeholder="e.g. Mobile Developer, Cloud Architect"
                           class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl p-3.5 font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                </div>
            @endif

            <button wire:click="startInterview"
                    class="w-full py-4 bg-gradient-to-r from-[#1E1B4B] to-[#4F46E5] hover:from-[#312E81] hover:to-[#6366F1] text-white font-black rounded-2xl shadow-xl shadow-indigo-500/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm">
                <span>🚀</span> Start Interview Session
            </button>

            {{-- Info pills --}}
            <div class="flex justify-center gap-3 mt-5 flex-wrap">
                <span class="text-[11px] font-bold text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-full">⏱ ~5 mins</span>
                <span class="text-[11px] font-bold text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-full">🤖 AI Feedback</span>
                <span class="text-[11px] font-bold text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-full">🏆 +75 XP</span>
            </div>
        </div>
    </div>

    {{-- ══ INTERVIEWING / GENERATING STATE ══ --}}
    @elseif($status === 'interviewing' || $status === 'generating_feedback')
    <div class="flex flex-col h-[calc(100vh-8rem)] max-h-[800px]">

        {{-- Header bar --}}
        <div class="bg-gradient-to-r from-[#1E1B4B] to-[#312E81] px-6 py-4 flex items-center justify-between rounded-t-3xl shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">🎤</div>
                <div>
                    <p class="text-white font-black text-sm leading-tight">Mock Interview</p>
                    <p class="text-indigo-300 text-[11px] font-semibold">{{ $selectedCareer }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Progress --}}
                <div class="hidden sm:flex items-center gap-2">
                    <span class="text-indigo-300 text-[11px] font-bold">Q{{ min($questionCount, $maxQuestions) }}/{{ $maxQuestions }}</span>
                    <div class="w-24 bg-white/10 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 bg-yellow-400 rounded-full transition-all duration-700"
                             style="width: {{ min(($questionCount / $maxQuestions) * 100, 100) }}%"></div>
                    </div>
                </div>
                <button wire:click="resetInterview"
                        class="px-3 py-1.5 bg-white/10 hover:bg-rose-500/40 text-white/70 hover:text-white text-[11px] font-bold rounded-xl transition-all">
                    End
                </button>
            </div>
        </div>

        {{-- Chat messages --}}
        <div class="flex-1 overflow-y-auto bg-[#F8F9FC] px-5 py-5 space-y-4" id="interviewChatContainer">

            @foreach($messages as $msg)
                @php $isAI = $msg['role'] === 'assistant'; @endphp
                <div class="flex {{ $isAI ? 'justify-start' : 'justify-end' }} items-end gap-2">

                    @if($isAI)
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#1E1B4B] to-[#4F46E5] flex items-center justify-center text-sm shrink-0 shadow">🤵</div>
                    @endif

                    <div class="max-w-[78%] flex flex-col {{ $isAI ? 'items-start' : 'items-end' }} gap-1">
                        @if($isAI)
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">AI Interviewer</span>
                        @endif
                        <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed font-medium
                            {{ $isAI
                                ? 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm shadow-sm'
                                : 'bg-gradient-to-br from-indigo-500 to-violet-600 text-white rounded-br-sm shadow-lg shadow-indigo-500/20' }}">
                            {!! nl2br(e($msg['content'])) !!}
                        </div>
                        @if(!$isAI)
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">You</span>
                        @endif
                    </div>

                    @if(!$isAI)
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-sm font-black text-white shrink-0 shadow">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                </div>
            @endforeach

            {{-- Typing indicator --}}
            <div wire:loading wire:target="sendMessage,startInterview" class="flex items-end gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#1E1B4B] to-[#4F46E5] flex items-center justify-center text-sm shrink-0">🤵</div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1.5 items-center">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>
            </div>

            {{-- Generating feedback spinner --}}
            @if($status === 'generating_feedback')
                <div class="flex justify-center py-6">
                    <div class="bg-white border border-indigo-100 rounded-2xl px-6 py-4 flex items-center gap-3 shadow-sm">
                        <svg class="animate-spin w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="font-bold text-sm text-gray-700">Analyzing your responses...</span>
                    </div>
                </div>
            @endif

        </div>

        {{-- Input bar --}}
        @if($status === 'interviewing')
        <div class="bg-white border-t border-gray-100 px-4 py-3 shrink-0 rounded-b-3xl">
            <form wire:submit.prevent="sendMessage" class="flex gap-2"
                  x-data="{
                      isListening: false,
                      recognition: null,
                      init() {
                          const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                          if (SpeechRecognition) {
                              this.recognition = new SpeechRecognition();
                              this.recognition.continuous = true;
                              this.recognition.interimResults = false;
                              this.recognition.lang = 'en-US';

                              this.recognition.onstart = () => { this.isListening = true; };
                              this.recognition.onend = () => { this.isListening = false; };
                              this.recognition.onerror = () => { this.isListening = false; };
                              this.recognition.onresult = (event) => {
                                  let transcript = '';
                                  for (let i = event.resultIndex; i < event.results.length; i++) {
                                      transcript += event.results[i][0].transcript;
                                  }
                                  // Update Livewire message property directly
                                  @this.set('newMessage', ((@this.get('newMessage') || '') + ' ' + transcript).trim());
                              };
                          }
                      },
                      toggleMic() {
                          if (!this.recognition) {
                              alert('Voice recognition not supported in this browser. Please use Chrome or Safari.');
                              return;
                          }
                          if (this.isListening) {
                              this.recognition.stop();
                          } else {
                              this.recognition.start();
                          }
                      }
                  }">
                <button type="button" @click="toggleMic()"
                        :class="isListening ? 'bg-red-500 hover:bg-red-600 animate-pulse text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-indigo-600'" 
                        class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"></path>
                    </svg>
                </button>
                <input type="text" wire:model="newMessage"
                       placeholder="Type your answer or speak using the microphone..."
                       wire:loading.attr="disabled"
                       class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl px-4 py-3 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none placeholder-gray-400">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/25 active:scale-90 transition-all disabled:opacity-50 shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </form>
        </div>
        @endif

    </div>

    {{-- ══ FEEDBACK STATE ══ --}}
    @elseif($status === 'feedback' && $feedbackData)
    <div class="max-w-3xl mx-auto w-full py-4">

        {{-- Score hero --}}
        @php $score = $feedbackData['overall_score'] ?? 0; @endphp
        <div class="bg-gradient-to-br
            {{ $score >= 80 ? 'from-emerald-500 to-teal-500' : ($score >= 60 ? 'from-indigo-500 to-violet-500' : 'from-amber-500 to-orange-500') }}
            rounded-3xl p-8 mb-6 text-white text-center relative overflow-hidden shadow-2xl">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full"></div>
            <div class="w-20 h-20 rounded-full bg-white/20 border-2 border-white/30 flex flex-col items-center justify-center mx-auto mb-4">
                <span class="text-3xl font-black leading-none">{{ $score }}</span>
                <span class="text-white/60 text-xs font-bold">/100</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight mb-1">Interview Complete!</h2>
            <p class="text-white/80 text-sm font-medium">Performance analysis for <strong class="text-white">{{ $selectedCareer }}</strong></p>
        </div>

        {{-- Overall feedback --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">📋 Overall Assessment</p>
            <p class="text-gray-700 text-sm font-medium leading-relaxed">{{ $feedbackData['detailed_feedback'] ?? '' }}</p>
        </div>

        {{-- Strengths & Improvements --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6">
                <p class="text-[11px] font-black text-emerald-600 uppercase tracking-widest mb-4 flex items-center gap-2">✅ Key Strengths</p>
                <ul class="space-y-2.5">
                    @foreach($feedbackData['strengths'] ?? [] as $s)
                        <li class="flex items-start gap-2 text-sm text-gray-700 font-medium">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">✓</span>
                            {{ $s }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-3xl border border-amber-100 shadow-sm p-6">
                <p class="text-[11px] font-black text-amber-600 uppercase tracking-widest mb-4 flex items-center gap-2">📈 Improve On</p>
                <ul class="space-y-2.5">
                    @foreach($feedbackData['areas_to_improve'] ?? [] as $a)
                        <li class="flex items-start gap-2 text-sm text-gray-700 font-medium">
                            <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">→</span>
                            {{ $a }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button wire:click="resetInterview"
                    class="flex-1 py-3.5 bg-[#1E1B4B] hover:bg-[#312E81] text-white font-black text-sm rounded-2xl transition-all active:scale-95 shadow-lg shadow-indigo-900/20">
                🔄 Try Another Interview
            </button>
            <a href="{{ route('student.skill-gap.index') }}"
               class="flex-1 py-3.5 bg-white border border-gray-200 hover:border-indigo-200 text-gray-900 font-black text-sm rounded-2xl transition-all active:scale-95 text-center hover:bg-indigo-50">
                🧠 Skill-Gap Analysis
            </a>
        </div>

    </div>
    @endif

    {{-- Scroll to bottom script --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const scroll = () => {
                let c = document.getElementById('interviewChatContainer');
                if (c) setTimeout(() => c.scrollTop = c.scrollHeight, 80);
            };
            Livewire.on('message-sent', scroll);
            scroll();
        });
    </script>

</div>
