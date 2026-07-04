<x-app-layout>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 pb-12">
        
        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl flex items-center gap-3">
                <span class="text-xl">⚠️</span>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
                <span class="text-xl">✅</span>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @livewire('student.mock-interview')

    </div>
</x-app-layout>
