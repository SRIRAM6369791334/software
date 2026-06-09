@extends('layouts.app')

@section('title', 'Sovereign Console')

@section('content')
<div class="relative min-h-screen">
    {{-- Decorative Orbs --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-400/10 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute top-1/2 -right-24 w-96 h-96 bg-sky-400/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
        <div>
            <h1 class="text-3xl font-black text-zinc-950 tracking-tight">Sovereign Console</h1>
            <p class="text-zinc-500 font-medium">Identity orchestration and system-wide security audit</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openModal('userModal')" 
                    class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-xl bg-zinc-950 px-6 py-4 text-sm font-black text-white shadow-2xl transition-all hover:scale-[1.02] active:scale-95">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-sky-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <span class="relative z-10 flex items-center gap-2">
                    <span class="material-symbols-rounded text-xl">person_add</span>
                    Deploy Agent
                </span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-10">
        
        {{-- Agents Monitor --}}
        <div class="lg:col-span-8 bg-white/60 backdrop-blur-xl rounded-[2.5rem] border border-white/40 shadow-xl shadow-zinc-200/40 overflow-hidden">
            <div class="p-8 border-b border-zinc-100 bg-gradient-to-r from-emerald-50/50 to-sky-50/50 flex items-center justify-between">
                <h3 class="font-black text-zinc-950 flex items-center gap-2 uppercase tracking-widest text-xs">
                    <span class="material-symbols-rounded text-emerald-600">monitor_heart</span>
                    Active System Agents
                </h3>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase">{{ $users->count() }} Profiles</span>
            </div>
            
            <div class="p-8 space-y-4">
                @foreach($users as $user)
                <div class="flex items-center gap-5 p-5 rounded-3xl bg-white/40 border border-zinc-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-sky-50 flex items-center justify-center text-xl font-black text-zinc-500 group-hover:from-emerald-600 group-hover:to-sky-500 group-hover:text-white transition-all shadow-sm">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-black text-zinc-950 tracking-tight text-lg">{{ $user->name }}</p>
                            <span class="text-[9px] px-2.5 py-1 rounded-lg bg-zinc-900 text-white uppercase font-black tracking-widest shadow-lg shadow-zinc-900/10">
                                {{ $user->roles->first()?->name ?? 'No Role' }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-400 font-bold tracking-tight">
                            {{ $user->email }} · <span class="text-zinc-300 italic font-medium">{{ $user->username }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="relative inline-flex h-6 w-12 shrink-0 cursor-pointer items-center rounded-full transition-all duration-300 {{ $user->is_active ? 'bg-emerald-500 shadow-lg shadow-emerald-500/20' : 'bg-zinc-200' }}">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition-transform {{ $user->is_active ? 'translate-x-7' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                        <button class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-zinc-200 text-zinc-400 hover:text-emerald-600 hover:border-emerald-100 hover:shadow-lg transition-all active:scale-90 shadow-sm">
                            <span class="material-symbols-rounded">settings</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Activity Stream --}}
        <!-- <div class="lg:col-span-4 bg-zinc-950 rounded-[2.5rem] shadow-2xl shadow-zinc-900/20 overflow-hidden text-white flex flex-col">
            <div class="p-8 border-b border-white/10 bg-white/5">
                <h3 class="font-black flex items-center gap-3 uppercase tracking-widest text-xs text-emerald-400">
                    <span class="material-symbols-rounded">analytics</span>
                    Neural Stream
                </h3>
            </div>
            
            <div class="p-8 flex-1 space-y-8 relative overflow-y-auto max-h-[600px] custom-scrollbar">
                <div class="absolute left-10 top-8 bottom-8 w-px bg-white/10"></div>
                
                @forelse($activityLogs ?? [] as $log)
                <div class="relative pl-12 group">
                    <div class="absolute left-[7px] top-1.5 w-3 h-3 rounded-full bg-zinc-950 border-2 border-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.4)] z-10 transition-transform group-hover:scale-125"></div>
                    <div class="space-y-1">
                        <p class="text-xs font-black text-white tracking-tight leading-none group-hover:text-emerald-400 transition-colors">{{ $log->action }}</p>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-tighter">{{ $log->module }} · {{ $log->timestamp->diffForHumans() }}</p>
                        <p class="text-[10px] text-emerald-500/70 font-black tracking-widest mt-1">SEC_LEVEL_0{{ rand(1,4) }} · {{ $log->user->name ?? 'System' }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-20">
                    <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white/20 mx-auto mb-4">
                        <span class="material-symbols-rounded text-3xl">sensors_off</span>
                    </div>
                    <p class="text-[10px] uppercase font-black tracking-[0.2em] text-white/30">No telemetry data</p>
                </div>
                @endforelse
            </div>

            <div class="p-8 mt-auto border-t border-white/10 bg-white/5">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">
                    <span>System Status</span>
                    <span class="flex items-center gap-1.5 text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Operational
                    </span>
                </div>
            </div>
        </div> -->

    </div>
</div>
@endsection
