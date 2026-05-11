@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="w-full max-w-md">
    <div class="mb-8 flex items-center justify-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-sky-500 text-white shadow-lg shadow-emerald-100">
            <span class="material-symbols-rounded text-3xl">egg</span>
        </div>
        <div>
            <h1 class="text-xl font-black tracking-tight text-slate-950">PoultryPro</h1>
            <p class="text-xs font-semibold text-slate-500">Management System</p>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 shadow-md shadow-slate-200/60">
        <h2 class="mb-1 text-xl font-black tracking-tight text-slate-950">Welcome back</h2>
        <p class="mb-6 text-sm font-medium text-slate-500">Sign in to your account</p>

        @if($errors->any())
            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-bold text-slate-700">Email</label>
                <input type="email" id="email" name="email" required autofocus
                       value="{{ old('email') }}"
                       placeholder="you@example.com"
                       class="w-full rounded-2xl border border-slate-200 bg-emerald-50 px-4 py-3 text-sm font-semibold focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-bold text-slate-700">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="Password"
                       class="w-full rounded-2xl border border-slate-200 bg-emerald-50 px-4 py-3 text-sm font-semibold focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" value="1"
                       class="rounded border-slate-300 text-primary focus:ring-primary">
                <label for="remember" class="text-sm font-medium text-slate-600">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full rounded-2xl bg-primary px-4 py-3 text-sm font-black text-white shadow-lg shadow-emerald-100 transition-colors duration-200 hover:bg-emerald-700">
                Sign In
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-xs font-semibold text-slate-400">PoultryPro v1.0 - Secure Access</p>
</div>
@endsection
