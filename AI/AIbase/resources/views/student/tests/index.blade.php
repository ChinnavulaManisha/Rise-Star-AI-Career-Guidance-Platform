<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">
                    Evaluate Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-cyan-500">Cognitive Potential</span> 🧠
                </h1>
                <p class="text-gray-500 mt-2 text-sm md:text-base font-medium">Our interactive, scientifically-grounded aptitude metrics will map your mathematical, verbal, logical, and analytical strengths.</p>
            </div>
            
            <form action="{{ route('student.tests.ai-generate') }}" method="POST" class="flex flex-col md:flex-row items-end md:items-center gap-3 w-full md:w-auto" 
                  x-data="{ 
                      isGenerating: false,
                      isListening: false,
                      recognition: null,
                      init() {
                          const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                          if (SpeechRecognition) {
                              this.recognition = new SpeechRecognition();
                              this.recognition.continuous = false;
                              this.recognition.interimResults = false;
                              this.recognition.lang = 'en-US';
                              this.recognition.onstart = () => { this.isListening = true; };
                              this.recognition.onend = () => { this.isListening = false; };
                              this.recognition.onerror = () => { this.isListening = false; };
                              this.recognition.onresult = (event) => {
                                  const text = event.results[0][0].transcript;
                                  document.getElementById('topic').value = text.replace(/\.$/, '');
                              };
                          }
                      },
                      toggleMic() {
                          if (!this.recognition) {
                              alert('Voice Recognition is not supported by your browser. Please try Google Chrome or Safari!');
                              return;
                          }
                          if (this.isListening) {
                              this.recognition.stop();
                          } else {
                              this.recognition.start();
                          }
                      }
                  }" @submit="isGenerating = true">
                @csrf
                <div class="flex flex-col gap-1.5 w-full md:w-auto">
                    <label for="topic" class="text-xs text-gray-500 font-bold ml-1 uppercase tracking-wider">Focus Topic (Optional)</label>
                    <div class="relative flex items-center w-full md:w-auto">
                        <input type="text" name="topic" id="topic" placeholder="Leave blank for general Aptitude..." 
                               class="bg-white border border-gray-200 rounded-xl pl-4 pr-11 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 min-w-[260px] shadow-sm outline-none transition-all w-full md:w-auto">
                        <button type="button" @click="toggleMic()" 
                                :class="isListening ? 'text-red-500 animate-pulse bg-red-50 scale-110 shadow-sm' : 'text-gray-400 hover:text-indigo-600 bg-transparent hover:scale-105'" 
                                class="absolute right-2 p-2 rounded-lg transition-all flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" :disabled="isGenerating" :class="isGenerating ? 'opacity-75 cursor-wait' : 'hover:scale-105 hover:shadow-indigo-500/20 active:scale-95'" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg border border-transparent">
                    <svg x-show="!isGenerating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <svg x-show="isGenerating" style="display: none;" class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isGenerating ? 'Generating... (~5s)' : 'Generate Adaptive AI Quiz'"></span>
                </button>
            </form>
        </div>

        @if(session('error'))
            <div class="mb-8 bg-red-50 border border-red-100 text-red-600 p-4 rounded-2xl flex items-center gap-3 font-medium shadow-sm">
                <span class="text-xl">⚠️</span>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if(session('success'))
            <div class="mb-8 bg-emerald-50 border border-emerald-100 text-emerald-600 p-4 rounded-2xl flex items-center gap-3 font-medium shadow-sm">
                <span class="text-xl">✨</span>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Career Interest Test Banner --}}
        @if(isset($interestTest) && $interestTest)
        <div class="mb-8 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
            <div class="text-white">
                <p class="text-xs font-bold uppercase tracking-widest text-violet-200 mb-1">🎯 Discover Your Path</p>
                <h3 class="text-xl font-bold">Career Interest Discovery Test</h3>
                <p class="text-violet-200 text-sm mt-1">10 quick questions to find which career domain fits your personality and interests.</p>
            </div>
            <a href="{{ route('student.tests.show', $interestTest->id) }}"
               class="shrink-0 px-6 py-3 bg-white text-indigo-700 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-md text-sm whitespace-nowrap">
                Take Interest Test →
            </a>
        </div>
        @endif

        <!-- Tests Grid -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
            
            @if($tests->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <div class="text-5xl mb-4">💤</div>
                    <h3 class="text-xl font-bold text-gray-900">Assessments Offline</h3>
                    <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto font-medium">There are currently no active aptitude test metrics available in the databank. Please click "Generate Adaptive AI Quiz" above to create one instantly.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tests as $test)
                        <div class="border border-gray-100 bg-gray-50 p-6 rounded-2xl flex flex-col justify-between hover:border-indigo-200 hover:bg-indigo-50/50 transition-all group shadow-sm hover:shadow-md">
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $test->title }}</h3>
                                </div>
                                <span class="inline-block mb-4 px-3 py-1 rounded-md text-[10px] font-black bg-indigo-100 text-indigo-700 border border-indigo-200 uppercase tracking-widest">
                                    @if($test->category === 'General') Aptitude Assessment @else {{ $test->category }} @endif
                                </span>
                                <p class="text-sm text-gray-600 font-medium leading-relaxed mb-6">
                                    {{ Str::limit($test->description, 100) }}
                                </p>
                                <div class="grid grid-cols-2 gap-4 bg-white p-4 rounded-xl border border-gray-100 text-xs text-gray-600 font-bold mb-6 shadow-sm">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-gray-400 uppercase tracking-wider text-[10px]">Duration</span>
                                        <div class="flex items-center gap-1.5 text-gray-900">
                                            <span>⏱️</span>
                                            {{ $test->duration_minutes }} Mins
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-gray-400 uppercase tracking-wider text-[10px]">Questions</span>
                                        <div class="flex items-center gap-1.5 text-gray-900">
                                            <span>❓</span>
                                            @if($test->category === 'General' && count($test->questions ?? []) > 15)
                                                15 random
                                            @else
                                                {{ count($test->questions ?? []) }} items
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('student.tests.show', $test->id) }}" class="w-full text-center bg-gray-900 hover:bg-gray-800 text-white py-3.5 rounded-xl font-bold transition-all shadow-md active:scale-[0.98]">
                                Start Evaluation
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>
</x-app-layout>
