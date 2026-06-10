<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PoultryPro') | Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Cabinet+Grotesk:wght@500;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.cm-style')
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js for Dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js for UI interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CDN for instant styles without npm run dev -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                        cabinet: ['Cabinet Grotesk', 'sans-serif'],
                        jetbrains: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        blob: "blob 10s infinite",
                    },
                    keyframes: {
                        blob: {
                            "0%": { transform: "translate(0px, 0px) scale(1)" },
                            "33%": { transform: "translate(30px, -50px) scale(1.1)" },
                            "66%": { transform: "translate(-20px, 20px) scale(0.9)" },
                            "100%": { transform: "translate(0px, 0px) scale(1)" },
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50 transition-colors duration-300 font-sans antialiased selection:bg-emerald-500/30 selection:text-emerald-900 dark:selection:bg-emerald-500/30 dark:selection:text-emerald-100 flex flex-col">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden relative">
    <!-- Overlay with Alpine Transition -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-40 bg-zinc-900/40 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"></div>

    @include('partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden bg-zinc-50 dark:bg-zinc-950 transition-colors duration-300">
        @include('partials.topbar')

        <main class="app-content flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8 relative bg-gradient-to-br from-emerald-50/50 via-zinc-50/80 to-cyan-50/50 dark:from-emerald-950 dark:via-zinc-950 dark:to-cyan-950">
            <!-- Glassmorphism Background Blobs -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none z-0 flex justify-center items-center">
                <div class="absolute top-0 -left-4 w-[500px] h-[500px] bg-emerald-300/40 dark:bg-emerald-800/30 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-100 animate-blob"></div>
                <div class="absolute top-0 -right-4 w-[500px] h-[500px] bg-cyan-300/40 dark:bg-cyan-800/30 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-100 animate-blob" style="animation-delay: 2s;"></div>
                <div class="absolute -bottom-32 left-20 w-[500px] h-[500px] bg-teal-300/40 dark:bg-teal-800/30 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-100 animate-blob" style="animation-delay: 4s;"></div>
            </div>
            
            <div class="mx-auto w-full max-w-[1500px] relative z-10">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<x-toast />
@stack('scripts')
@stack('modals')
</body>
</html>
