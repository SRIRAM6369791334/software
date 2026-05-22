<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PoultryPro') | Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full bg-background text-foreground font-sans antialiased selection:bg-primary/10 selection:text-primary">

<div id="page-skeleton" class="page-skeleton fixed inset-0 z-[999] bg-white/90 backdrop-blur-md">
    <div class="flex h-full">
        <div class="hidden w-72 border-r border-emerald-100 bg-gradient-to-b from-emerald-50 to-sky-50 p-5 lg:block">
            <div class="mb-8 flex items-center gap-3">
                <div class="skeleton-pulse h-12 w-12 rounded-2xl"></div>
                <div class="space-y-2">
                    <div class="skeleton-pulse h-4 w-28 rounded-full"></div>
                    <div class="skeleton-pulse h-3 w-36 rounded-full"></div>
                </div>
            </div>
            <div class="space-y-3">
                @for($i = 0; $i < 10; $i++)
                    <div class="skeleton-pulse h-10 rounded-xl"></div>
                @endfor
            </div>
        </div>
        <div class="flex flex-1 flex-col">
            <div class="h-20 border-b border-emerald-100 bg-white/80 px-8 py-5">
                <div class="flex items-center justify-between">
                    <div class="skeleton-pulse h-10 w-80 rounded-2xl"></div>
                    <div class="flex gap-3">
                        <div class="skeleton-pulse h-10 w-10 rounded-xl"></div>
                        <div class="skeleton-pulse h-10 w-10 rounded-xl"></div>
                    </div>
                </div>
            </div>
            <div class="grid gap-6 p-8 md:grid-cols-2 xl:grid-cols-4">
                @for($i = 0; $i < 8; $i++)
                    <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                        <div class="skeleton-pulse mb-5 h-12 w-12 rounded-2xl"></div>
                        <div class="skeleton-pulse mb-3 h-4 w-28 rounded-full"></div>
                        <div class="skeleton-pulse h-8 w-36 rounded-full"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<div class="flex h-screen overflow-hidden">
    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/35 backdrop-blur-sm lg:hidden"
         onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

    @include('partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
        @include('partials.topbar')

        <main class="app-content flex-1 overflow-y-auto px-4 py-5 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-[1500px]">
                @include('partials.flash-messages')
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
