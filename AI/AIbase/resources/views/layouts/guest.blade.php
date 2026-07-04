<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'RiseStar AI') }}</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased bg-white text-gray-900 selection:bg-indigo-500 selection:text-white">
        <div class="flex min-h-screen">
            
            <!-- Left Side - Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 lg:p-24 relative z-10 bg-white">
                <div class="w-full max-w-md">
                    <!-- Logo -->
                    <div class="mb-10">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-black text-2xl shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition-transform">
                                R
                            </div>
                            <span class="text-2xl font-black tracking-tight text-gray-900">RiseStar<span class="text-indigo-600">AI</span></span>
                        </a>
                    </div>
                    
                    {{ $slot }}
                    
                </div>
            </div>

            <!-- Right Side - Graphic -->
            <div class="hidden lg:flex lg:w-1/2 bg-slate-50 relative items-center justify-center overflow-hidden border-l border-gray-100">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdib3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiM0ZjQ2ZTUiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djIwaC0ydi0yMEg1VjUwaDIwVjUwaDJ2MjBIM2EyIDIgMCAwMS0yLTJWMy45QTIgMiAwIDAxMyAxLjlIMzlhMiAyIDAgMDExLjkuOXYyMFgzNnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-50"></div>
                
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-600/5 rounded-full blur-3xl"></div>
                <div class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-sky-400/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 max-w-lg text-center p-12">
                    <div class="bg-white p-8 rounded-3xl shadow-2xl shadow-gray-200/50 border border-gray-100 mb-8 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-bold text-gray-900">AI Blueprint Engine</div>
                                <div class="text-xs text-gray-500">Mapping your future</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-2 bg-gray-100 rounded-full w-full"></div>
                            <div class="h-2 bg-gray-100 rounded-full w-5/6"></div>
                            <div class="h-2 bg-indigo-100 rounded-full w-4/6"></div>
                        </div>
                    </div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-4">Discover Your Path.</h2>
                    <p class="text-lg text-gray-500 font-medium">Join thousands of students navigating their careers with advanced AI guidance.</p>
                </div>
            </div>

        </div>
    </body>
</html>
