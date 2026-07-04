<div x-data="{
        open: false,
        scrollToBottom() {
            let box = this.$refs.chatBox;
            if (box) box.scrollTop = box.scrollHeight;
        }
     }"
     x-init="$wire.on('message-sent', () => { setTimeout(() => scrollToBottom(), 80); })"
     class="fixed bottom-6 right-6 z-50 flex flex-col items-end">

    <!-- ===== CHAT WINDOW ===== -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="mb-4 w-[370px] sm:w-[420px] flex flex-col rounded-[2rem] overflow-hidden shadow-2xl shadow-black/30 border border-white/10"
         style="height: 580px; display: none;">

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#1E1B4B] to-[#312E81] px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <!-- Bot Avatar -->
                <div class="relative">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg shadow-orange-500/30">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                        </svg>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-[#1E1B4B] animate-pulse"></span>
                </div>
                <div>
                    <h2 class="text-white font-black text-base leading-tight">CareerDesk Bot</h2>
                    <p class="text-indigo-300 text-[10px] font-bold uppercase tracking-widest flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        RiseStar AI • Online
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Clear Chat -->
                <button wire:click="clearChat" title="Clear chat"
                        class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-indigo-300 hover:text-white transition-all flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                <!-- Close -->
                <button @click="open = false"
                        class="w-8 h-8 rounded-xl bg-white/10 hover:bg-rose-500/60 text-indigo-300 hover:text-white transition-all flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Subheader hint strip -->
        <div class="bg-indigo-900/80 backdrop-blur-sm px-5 py-2 flex items-center gap-2 border-b border-white/5 shrink-0">
            <span class="text-yellow-400 text-xs">✦</span>
            <p class="text-indigo-300 text-[11px] font-semibold">Ask me anything about careers, skills, or your future path!</p>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4 bg-[#0F0E1A]" x-ref="chatBox">

            @if(empty($messages))
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center pb-6">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-indigo-500/20 to-violet-600/20 border border-indigo-500/20 flex items-center justify-center text-3xl mb-4">🎓</div>
                    <h3 class="text-white font-black text-base">Hi, I'm CareerDesk!</h3>
                    <p class="text-indigo-400 text-sm font-medium mt-2 max-w-[220px] leading-relaxed">Your AI career counselor. Ask me anything to get started.</p>

                    <!-- Suggestion chips -->
                    <div class="mt-5 flex flex-wrap gap-2 justify-center">
                        <button wire:click="sendSuggestion('What career suits me based on my test scores?')"
                                class="text-[11px] font-bold bg-indigo-900/60 border border-indigo-700/60 text-indigo-300 hover:bg-indigo-700 hover:text-white px-3 py-1.5 rounded-xl transition-all">
                            What career suits me?
                        </button>
                        <button wire:click="sendSuggestion('How do I improve my aptitude score?')"
                                class="text-[11px] font-bold bg-indigo-900/60 border border-indigo-700/60 text-indigo-300 hover:bg-indigo-700 hover:text-white px-3 py-1.5 rounded-xl transition-all">
                            Improve aptitude score
                        </button>
                        <button wire:click="sendSuggestion('What skills do I need for software engineering?')"
                                class="text-[11px] font-bold bg-indigo-900/60 border border-indigo-700/60 text-indigo-300 hover:bg-indigo-700 hover:text-white px-3 py-1.5 rounded-xl transition-all">
                            Skills for Software Eng.
                        </button>
                    </div>
                </div>
            @endif

            @foreach($messages as $i => $msg)
                @php $isAi = ($msg['role'] ?? 'user') === 'assistant'; @endphp

                <div class="flex {{ $isAi ? 'justify-start' : 'justify-end' }} items-end gap-2">

                    {{-- AI Avatar --}}
                    @if($isAi)
                        <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-xs font-black shrink-0 shadow-md mb-0.5">
                            C
                        </div>
                    @endif

                    <div class="max-w-[78%] flex flex-col gap-1 {{ $isAi ? 'items-start' : 'items-end' }}">
                        <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                            {{ $isAi
                                ? 'bg-white/10 border border-white/10 text-gray-200 rounded-bl-sm'
                                : 'bg-gradient-to-br from-indigo-500 to-violet-600 text-white rounded-br-sm shadow-lg' }}">
                            @if($isAi)
                                @php
                                    $text = $msg['content'] ?? '';
                                    // Bold **text**
                                    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
                                    // Italic *text*
                                    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
                                    // Bullet points
                                    $text = preg_replace('/^[•\-\*] (.+)$/m', '<li class="ml-3">$1</li>', $text);
                                    // Line breaks
                                    $text = nl2br($text);
                                @endphp
                                {!! $text !!}
                            @else
                                {{ $msg['content'] ?? '' }}
                            @endif
                        </div>
                        <span class="text-[10px] text-gray-600 font-medium px-1">
                            {{ $isAi ? 'CareerDesk' : 'You' }}
                        </span>
                    </div>

                    {{-- User Avatar --}}
                    @if(!$isAi)
                        <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white text-xs font-black shrink-0 mb-0.5">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                </div>
            @endforeach

            <!-- Typing Indicator -->
            <div wire:loading wire:target="sendMessage" class="flex items-end gap-2 justify-start">
                <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-xs font-black shrink-0">C</div>
                <div class="bg-white/8 border border-white/8 backdrop-blur-sm px-4 py-3 rounded-2xl rounded-bl-sm">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#0F0E1A] border-t border-white/5 px-4 py-3 shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <input
                    type="text"
                    wire:model="newMessage"
                    placeholder="Ask about careers, skills, or paths..."
                    wire:loading.attr="disabled"
                    autocomplete="off"
                    class="flex-1 bg-white/5 border border-white/10 text-white text-sm rounded-2xl px-4 py-3 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                >
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white rounded-2xl flex items-center justify-center shadow-lg active:scale-90 transition-all disabled:opacity-50 shrink-0">
                    <svg wire:loading.remove wire:target="sendMessage" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    <svg wire:loading wire:target="sendMessage" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </button>
            </form>
            <p class="text-center text-[10px] text-gray-700 font-medium mt-2">Powered by Gemini AI · RiseStar</p>
        </div>
    </div>

    <!-- ===== FAB BUTTON ===== -->
    <button @click="open = !open; if(open) setTimeout(() => scrollToBottom(), 150)"
            class="relative w-16 h-16 bg-gradient-to-br from-[#1E1B4B] to-[#4F46E5] rounded-2xl shadow-2xl shadow-indigo-900/50 flex items-center justify-center hover:scale-105 active:scale-95 transition-all border border-indigo-500/30 group">

        <!-- Pulse ring -->
        <span class="absolute inset-0 rounded-2xl bg-indigo-500/20 animate-ping opacity-0 group-hover:opacity-100"></span>

        <!-- Notification dot -->
        <span x-show="!open" class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full border-2 border-[#F4F5F7] flex items-center justify-center">
            <span class="text-[7px] text-white font-black">AI</span>
        </span>

        <!-- Chat icon -->
        <svg x-show="!open" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        <!-- Close icon -->
        <svg x-show="open" style="display: none;" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
