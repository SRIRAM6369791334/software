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
    @include('partials.cm-style')
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full bg-slate-50 text-slate-900 font-sans antialiased selection:bg-indigo-500/20 selection:text-indigo-900">

<div class="flex h-screen overflow-hidden">
    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/20 backdrop-blur-sm lg:hidden transition-opacity"
         onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

    @include('partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden bg-slate-50">
        @include('partials.topbar')

        <main class="app-content flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
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
