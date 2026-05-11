@extends('layouts.app')

@section('title', 'Sovereign Console')

@section('content')
<div class="relative min-h-screen">
    <div class="glow-orb w-[600px] h-[600px] bg-primary/10 top-[-300px] left-[-300px]"></div>

    <div class="page-header relative z-10">
        <div>
            <h1 class="page-title gradient-text">Sovereign Console</h1>
            <p class="page-subtitle">Identity orchestration and system-wide audit trails.</p>
        </div>
        <button onclick="openModal('userModal')" class="bg-gradient-to-r from-emerald-600 to-sky-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition-all flex items-center gap-2">
            <span></span> Deploy Agent
        </button>
    </div>

    <div class="bento-grid relative z-10">
        
        {{-- Agents Monitor --}}
        <div class="bento-item col-span-1 md:col-span-2 lg:col-span-4 xl:col-span-4">
            <h3 class="font-bold mb-6 flex items-center gap-2">
                <span class="text-primary"></span> Active Agents
            </h3>
            
            <div class="space-y-4">
                @foreach($users as $user)
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-muted/20 border border-transparent hover:border-border transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center text-lg font-bold text-primary">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-bold">{{ $user->name }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary uppercase font-bold tracking-tighter">
                                {{ $user->roles->first()?->name ?? 'No Role' }}
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground">{{ $user->email }} · @ {{ $user->username }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer items-center rounded-full transition-colors {{ $user->is_active ? 'bg-success' : 'bg-muted' }}">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform {{ $user->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                        <button class="p-2 hover:bg-muted rounded-xl transition-all">⚙
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Activity Stream --}}
        <div class="bento-item col-span-1 md:col-span-2 lg:col-span-2 xl:col-span-2">
            <h3 class="font-bold mb-6 flex items-center gap-2">
                <span class="text-primary"></span> Neural Stream
            </h3>
            <div class="space-y-6 relative before:absolute before:left-4 before:top-2 before:bottom-2 before:w-px before:bg-border">
                @foreach($activityLogs ?? [] as $log)
                <div class="relative pl-10">
                    <div class="absolute left-[13px] top-1.5 w-2 h-2 rounded-full bg-primary border-4 border-card shadow-[0_0_0_4px_rgba(var(--primary),0.1)]"></div>
                    <p class="text-xs font-bold leading-none">{{ $log->action }}</p>
                    <p class="text-[10px] text-muted-foreground mt-1">{{ $log->module }} · {{ $log->timestamp->diffForHumans() }}</p>
                    <p class="text-[10px] text-primary/70 mt-0.5">by {{ $log->user->name ?? 'System' }}</p>
                </div>
                @endforeach
                @if(empty($activityLogs))
                <div class="text-center py-10 opacity-30">
                    <span class="text-4xl block mb-2"></span>
                    <p class="text-[10px] uppercase font-bold">No telemetry data</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
