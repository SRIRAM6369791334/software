@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="flex items-center justify-center gap-3 mb-8">
        <div class="h-12 w-12 flex items-center justify-center rounded-2xl bg-emerald-600 shadow-lg text-2xl">🥚</div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">PoultryPro</h1>
            <p class="text-xs text-gray-500">Management System</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Welcome back</h2>
        <p class="text-sm text-gray-500 mb-6">Sign in to your account</p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required autofocus
                       value="{{ old('email') }}"
                       placeholder="you@example.com"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                              @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="••••••••"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" value="1"
                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label for="remember" class="text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700
                           text-white font-semibold text-sm transition-colors duration-200 shadow-sm">
                Sign In
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">PoultryPro v1.0 • Secure Access</p>
</div>
@endsection
