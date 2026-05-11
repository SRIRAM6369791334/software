@extends('layouts.app')

@section('title', 'Biological Asset Management')

@section('content')
<div class="relative min-h-screen">
    <div class="glow-orb w-[400px] h-[400px] bg-sky-500/10 top-[-100px] right-[-100px]"></div>

    <div class="page-header relative z-10">
        <div>
            <h1 class="page-title gradient-text">Batch Evolution</h1>
            <p class="page-subtitle">Monitoring lifecycle dynamics and biological stability.</p>
        </div>
        <button onclick="openModal('batchModal')" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition-all flex items-center gap-2">
            <span>🥚</span> New Batch
        </button>
    </div>

    <div class="bento-grid relative z-10">
        @forelse($batches as $batch)
        <div class="bento-item col-span-1 md:col-span-3">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold">{{ $batch->batch_name }}</h3>
                    <p class="text-xs text-muted-foreground uppercase tracking-widest mt-1">Arrival: {{ $batch->date_received->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-muted-foreground uppercase font-bold">Health Score</p>
                    @php $health = ($batch->current_count / $batch->initial_count) * 100; @endphp
                    <p class="text-lg font-bold {{ $health > 95 ? 'text-success' : ($health > 90 ? 'text-warning' : 'text-red-500') }}">
                        {{ number_format($health, 1) }}%
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="p-3 bg-muted/30 rounded-xl">
                    <p class="text-[10px] text-muted-foreground uppercase font-bold">Initial</p>
                    <p class="text-lg font-bold">{{ number_format($batch->initial_count) }}</p>
                </div>
                <div class="p-3 bg-muted/30 rounded-xl">
                    <p class="text-[10px] text-muted-foreground uppercase font-bold">Current</p>
                    <p class="text-lg font-bold text-primary">{{ number_format($batch->current_count) }}</p>
                </div>
                <div class="p-3 bg-muted/30 rounded-xl">
                    <p class="text-[10px] text-muted-foreground uppercase font-bold">Mortality</p>
                    <p class="text-lg font-bold text-red-500">{{ number_format($batch->initial_count - $batch->current_count) }}</p>
                </div>
            </div>

            <div class="flex gap-2">
                <button onclick="openMortalityModal({{ $batch->id }}, '{{ $batch->batch_name }}')" class="flex-1 bg-red-500/10 text-red-500 py-2 rounded-lg text-xs font-bold hover:bg-red-500/20 transition-all">
                    Record Mortality
                </button>
                <button class="px-3 bg-muted py-2 rounded-lg text-xs font-bold hover:bg-border transition-all">
                    Details
                </button>
            </div>
        </div>
        @empty
        <div class="bento-item col-span-1 md:col-span-6 flex flex-col items-center justify-center py-20 text-center">
            <span class="text-6xl mb-4 opacity-20">🐣</span>
            <h3 class="text-xl font-bold">Swarm Empty</h3>
            <p class="text-muted-foreground max-w-xs mt-2">No active biological batches detected in the system. Initialize a new batch to begin tracking.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
