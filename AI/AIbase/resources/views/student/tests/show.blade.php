<x-app-layout>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 pb-12">

        {{-- Header + Timer --}}
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm gap-4"
             x-data="timerData({{ $test->duration_minutes }})" x-init="startTimer()">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $test->title }}</h1>
                <p class="text-xs text-gray-400 mt-1">
                    @if($test->category === 'Interest')
                        You can select <strong>multiple options</strong> per question — choose all that apply.
                    @else
                        Answer all questions and click Submit Assessment.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3 bg-red-50 border border-red-100 px-5 py-3 rounded-xl w-full sm:w-auto justify-between sm:justify-start">
                <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Time Left:</span>
                <span class="text-xl font-bold text-red-600 font-mono" x-text="timeDisplay">15:00</span>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mb-6 bg-white rounded-2xl p-4 border border-gray-100 shadow-sm" x-data="progressTracker({{ $questions->count() }})">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-gray-500">Progress</span>
                <span class="text-xs font-bold text-indigo-600" x-text="answered + ' / {{ $questions->count() }} answered'"></span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300"
                     :style="'width:' + (answered / {{ $questions->count() }} * 100) + '%'"></div>
            </div>
        </div>

        {{-- Test Form --}}
        <form id="test-form" action="{{ route('student.tests.submit', $test->id) }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="time_taken" id="time_taken" value="0">
            {{-- Store selected question IDs in form so session isn't needed --}}
            @foreach($questions as $q)
                <input type="hidden" name="question_ids[]" value="{{ $q->id }}">
            @endforeach

            @foreach($questions as $index => $question)
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm transition-all"
                 id="q-block-{{ $question->id }}">
                <div class="flex gap-4 items-start mb-5">
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-sm font-bold text-indigo-700 shrink-0">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-indigo-500 font-bold">{{ $question->category }}</span>
                        <h3 class="text-base font-semibold text-gray-900 mt-1 leading-relaxed">{{ $question->question_text }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($question->options as $key => $optionText)
                    @if($test->category === 'Interest')
                    {{-- Checkboxes for Interest test (multiple selection) --}}
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50
                                  hover:bg-violet-50 hover:border-violet-300 cursor-pointer transition-all group
                                  has-[:checked]:bg-violet-50 has-[:checked]:border-violet-400">
                        <input type="checkbox"
                               name="answers[{{ $question->id }}][]"
                               value="{{ $optionText }}"
                               onchange="markAnswered('{{ $question->id }}')"
                               class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                            <strong class="text-violet-400 mr-1.5">{{ chr(65 + $loop->index) }}.</strong>{{ $optionText }}
                        </span>
                    </label>
                    @else
                    {{-- Radio buttons for Aptitude test (single selection) --}}
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50
                                  hover:bg-indigo-50 hover:border-indigo-300 cursor-pointer transition-all group
                                  has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-400">
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $optionText }}"
                               onchange="markAnswered('{{ $question->id }}')"
                               class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                            <strong class="text-indigo-400 mr-1.5">{{ chr(65 + $loop->index) }}.</strong>{{ $optionText }}
                        </span>
                    </label>
                    @endif
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="flex justify-end pt-4">
                <button type="button" id="submit-btn"
                        onclick="submitTest()"
                        class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-white transition-all shadow-lg active:scale-95 text-base">
                    Submit Assessment ✓
                </button>
            </div>
        </form>
    </div>

    <script>
        // Timer
        function timerData(minutes) {
            return {
                secondsRemaining: minutes * 60,
                timeDisplay: '',
                timerInterval: null,
                startTimer() {
                    this.updateDisplay();
                    let start = Date.now();
                    this.timerInterval = setInterval(() => {
                        let diff = Math.floor((Date.now() - start) / 1000);
                        this.secondsRemaining = (minutes * 60) - diff;
                        document.getElementById('time_taken').value = diff;
                        if (this.secondsRemaining <= 0) {
                            clearInterval(this.timerInterval);
                            this.secondsRemaining = 0;
                            this.updateDisplay();
                            submitTest();
                        } else {
                            this.updateDisplay();
                        }
                    }, 1000);
                },
                updateDisplay() {
                    let m = Math.floor(this.secondsRemaining / 60);
                    let s = this.secondsRemaining % 60;
                    this.timeDisplay = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                }
            }
        }

        // Progress tracker
        const answeredSet = new Set();
        function progressTracker(total) {
            return {
                get answered() { return answeredSet.size; }
            };
        }
        function markAnswered(qid) {
            answeredSet.add(qid);
            // Update Alpine progress
            document.querySelectorAll('[x-data]').forEach(el => {
                if (el.__x) el.__x.updateElements(el);
            });
        }

        // Submit — prevent double submit completely
        let isSubmitting = false;
        function submitTest() {
            if (isSubmitting) return;
            isSubmitting = true;
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Submitting...';
            document.getElementById('test-form').submit();
        }
    </script>
</x-app-layout>
