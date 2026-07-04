<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Create an account</h1>
        <p class="text-gray-500 font-medium text-sm">Start your journey with AI-guided career planning.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white shadow-sm">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white shadow-sm">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="role" class="block text-sm font-bold text-gray-700 mb-1.5">I am a...</label>
                <select id="role" name="role" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white cursor-pointer shadow-sm">
                    <option value="student">Student</option>
                    <option value="counselor">Counselor</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div>
                <label for="grade" class="block text-sm font-bold text-gray-700 mb-1.5">Grade Level</label>
                <select id="grade" name="grade" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white cursor-pointer shadow-sm">
                    <option value="11">Grade 11</option>
                    <option value="12">Grade 12</option>
                    <option value="UG">Undergraduate</option>
                </select>
                <x-input-error :messages="$errors->get('grade')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white shadow-sm">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">Confirm</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white shadow-sm">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <button type="submit" class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all active:scale-[0.98] mt-4">
            Create account
        </button>

        <p class="text-center text-sm text-gray-600 font-medium mt-6">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-gray-900 font-bold hover:underline">Sign in</a>
        </p>
    </form>
</x-guest-layout>
