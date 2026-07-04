<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RiseStar AI — Future-Focused Cognitive Aptitude & Career Platform</title>

        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .hero-bg {
                background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                            radial-gradient(circle at bottom left, rgba(56, 189, 248, 0.08) 0%, transparent 40%);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-white selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col hero-bg">
        
        <!-- Navigation Bar -->
        <header class="w-full max-w-7xl mx-auto px-6 py-4 flex justify-between items-center z-50">
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-black text-xl shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition-transform">
                    R
                </div>
                <span class="text-xl font-black tracking-tight text-gray-900">RiseStar<span class="text-indigo-600">AI</span></span>
            </a>
            
            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20 active:scale-95">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors px-4 py-2.5">
                            Sign in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-gray-900/20 active:scale-95">
                                Get Started Free
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-6 lg:px-8 py-6 md:py-10 flex flex-col lg:flex-row items-center gap-10 z-40 grow w-full">
            <div class="flex-1 space-y-8 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    AI-Powered Career Mapping Engine
                </span>
                
                <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-[1.1] text-gray-900">
                    Discover your future with <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-cyan-500">absolute clarity.</span>
                </h1>
                
                <p class="text-gray-500 text-base md:text-lg max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    Stop guessing. We analyze your cognitive aptitude and personality through advanced assessments to perfectly map out your highest-potential career pathways.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start pt-1">
                    <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-7 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-white transition-all shadow-xl shadow-indigo-600/20 active:scale-95 text-base">
                        Take Assessment Now
                    </a>
                    <a href="#features" class="inline-flex justify-center items-center px-7 py-3.5 rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-bold transition-all active:scale-95 text-base">
                        See How It Works
                    </a>
                </div>
            </div>

            <!-- Visual Floating Card -->
            <div class="flex-1 w-full max-w-lg lg:max-w-none flex justify-center lg:justify-end relative">
                
                <!-- Background decorative elements -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdib3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiM0ZjQ2ZTUiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djIwaC0ydi0yMEg1VjUwaDIwVjUwaDJ2MjBIM2EyIDIgMCAwMS0yLTJWMy45QTIgMiAwIDAxMyAxLjlIMzlhMiAyIDAgMDExLjkuOXYyMFgzNnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-40 z-0"></div>

                <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-2xl shadow-gray-200/50 relative z-10 w-full max-w-md transform lg:-rotate-2 hover:rotate-0 transition-transform duration-500">
                    
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-cyan-400 flex items-center justify-center text-white font-bold shadow-md">
                                SJ
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Student Profile</h3>
                                <p class="text-xs text-gray-500 font-medium">Grade 12 • Science Stream</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold uppercase text-emerald-600 tracking-wider bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100">Live Sync</span>
                    </div>
                    
                    <!-- Progress Grid -->
                    <div class="space-y-4 mb-5">
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-gray-700">Logical Reasoning</span>
                                <span class="text-indigo-600">92%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 92%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-gray-700">Numerical Ability</span>
                                <span class="text-indigo-600">84%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full opacity-80" style="width: 84%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Counseling Simulated Conversation -->
                    <div class="bg-gray-50 border border-gray-100 p-5 rounded-2xl relative">
                        <div class="absolute -top-3 -right-3 w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm">
                            ✨
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <span class="text-[10px] font-bold tracking-widest text-indigo-600 uppercase">AI Blueprint Engine</span>
                        </div>
                        <p class="text-sm text-gray-600 font-medium leading-relaxed">"Based on your high logical scoring index, you show a strong cognitive potential for Data Science & Financial Engineering roles. Let's map your university roadmap."</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row justify-between items-center border-t border-gray-100 text-sm font-medium text-gray-500 bg-white relative z-50">
            <p>© {{ date('Y') }} RiseStar AI. Empowering the next generation.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-gray-900 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Terms of Service</a>
            </div>
        </footer>

    </body>
</html>
