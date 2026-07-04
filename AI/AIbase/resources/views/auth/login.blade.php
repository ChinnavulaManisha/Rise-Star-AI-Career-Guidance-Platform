<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Welcome back</h1>
        <p class="text-gray-500 font-medium text-sm">Enter your credentials to access your dashboard.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white shadow-sm">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-bold text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-bold text-indigo-600 hover:text-indigo-500 transition-colors" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-gray-900 bg-gray-50 hover:bg-white focus:bg-white shadow-sm">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Remember me for 30 days</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3.5 px-4 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl shadow-lg shadow-gray-900/20 transition-all active:scale-[0.98] mt-2">
            Sign in to account
        </button>

        <p class="text-center text-sm text-gray-600 font-medium mt-6">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline">Create an account</a>
        </p>
    </form>
</x-guest-layout>
