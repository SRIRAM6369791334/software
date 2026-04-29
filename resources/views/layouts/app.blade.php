<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PoultryPro') | Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ── Mobile Overlay ── --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/25 backdrop-blur-sm lg:hidden hidden"
         onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

    {{-- ── Sidebar ── --}}
    @include('partials.sidebar')

    {{-- ── Main Column ── --}}
    <div class="flex flex-1 flex-col overflow-hidden">

        {{-- Topbar --}}
        @include('partials.topbar')

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            @include('partials.flash-messages')
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
