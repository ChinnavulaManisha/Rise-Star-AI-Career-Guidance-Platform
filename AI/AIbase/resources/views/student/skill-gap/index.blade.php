<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center text-xl shadow-inner border border-white">
                🧠
            </span>
            Skill-Gap Analysis
        </h2>
    </x-slot>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if(session('error'))
            <div class="mb-6 bg-rose-50/80 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3 backdrop-blur-sm shadow-sm">
                <span class="text-xl">⚠️</span>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        @livewire('student.skill-gap')

    </div>
</x-app-layout>
