@extends('layouts.app')

@section('title', 'Route Management')

@section('content')
<div class="relative min-h-screen">
    {{-- Glow System Orbs --}}
    <div class="glow-orb w-[400px] h-[400px] bg-primary/20 top-[-100px] right-[-100px]"></div>
    <div class="glow-orb w-[300px] h-[300px] bg-emerald-500/10 bottom-[-50px] left-[-50px]"></div>

    <div class="page-header relative z-10">
        <div>
            <h1 class="page-title gradient-text">Neural Logistics</h1>
            <p class="page-subtitle">Real-time route monitoring and fleet orchestration.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('vehicleModal')" class="bg-gradient-to-r from-emerald-600 to-sky-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition-all flex items-center gap-2">
                <span></span> Add Vehicle
            </button>
            <button onclick="openModal('routeModal')" class="bg-card border text-foreground px-4 py-2 rounded-xl text-sm font-semibold hover:bg-muted transition-all flex items-center gap-2">
                <span></span> New Route
            </button>
        </div>
    </div>

    {{-- Bento Grid Dashboard --}}
    <div class="bento-grid relative z-10">
        
        {{-- Heartbeat Visualization (Neural Telemetry) --}}
        <div class="bento-item col-span-1 md:col-span-2 lg:col-span-2 xl:col-span-2 flex flex-col items-center justify-center text-center">
            <div class="neural-pulse w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <span class="text-4xl"></span>
            </div>
            <h3 class="text-xl font-bold">Fleet Connectivity</h3>
            <p class="text-xs text-muted-foreground mt-2 uppercase tracking-widest">System Online · {{ count($vehicles) }} Units</p>
            <div class="mt-6 flex gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-success">{{ $vehicles->count() }}</p>
                    <p class="text-[10px] text-muted-foreground uppercase">Vehicles</p>
                </div>
                <div class="w-px h-8 bg-border"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-warning">{{ $drivers->count() }}</p>
                    <p class="text-[10px] text-muted-foreground uppercase">Drivers</p>
                </div>
            </div>
        </div>

        {{-- Active Routes Monitor --}}
        <div class="bento-item col-span-1 md:col-span-2 lg:col-span-4 xl:col-span-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold flex items-center gap-2">
                    <span class="text-primary"></span> Active Network
                </h3>
                <span class="text-[10px] bg-primary/10 text-primary px-2 py-1 rounded-full uppercase font-bold tracking-tighter">Live Monitor</span>
            </div>
            
            <div class="space-y-4">
                @forelse($routes as $route)
                <div class="group flex items-center gap-4 p-3 rounded-xl hover:bg-muted/50 transition-all border border-transparent hover:border-border">
                    <div class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center text-lg shrink-0 group-hover:bg-primary/20 group-hover:text-primary transition-all">
                        
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate">{{ $route->route_name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $route->vehicle->vehicle_number ?? 'No Vehicle' }} · {{ $route->driver->driver_name ?? 'No Driver' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold">{{ ($route->customers_count ?? 0) + ($route->dealers_count ?? 0) }} Drops</p>
                        <div class="flex gap-1 justify-end mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-center text-muted-foreground py-8">No active routes detected in the swarm.</p>
                @endforelse
            </div>
        </div>

        {{-- Vehicle Fleet --}}
        <div class="bento-item col-span-1 md:col-span-2 lg:col-span-3 xl:col-span-3">
            <h3 class="font-bold mb-4 flex items-center gap-2">
                <span class="text-primary"></span> Fleet Status
            </h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach($vehicles as $vehicle)
                <div class="p-3 border rounded-xl bg-muted/20 relative group">
                    <div class="absolute top-2 right-2 w-2 h-2 rounded-full bg-success shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                    <p class="text-[10px] text-muted-foreground uppercase font-bold">{{ $vehicle->vehicle_type }}</p>
                    <p class="text-xs font-bold mt-1">{{ $vehicle->vehicle_number }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Driver Roster --}}
        <div class="bento-item col-span-1 md:col-span-2 lg:col-span-3 xl:col-span-3">
            <h3 class="font-bold mb-4 flex items-center gap-2">
                <span class="text-primary"></span> Personnel
            </h3>
            <div class="space-y-3">
                @foreach($drivers as $driver)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary">
                        {{ substr($driver->driver_name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate">{{ $driver->driver_name }}</p>
                        <p class="text-[10px] text-muted-foreground">{{ $driver->phone }}</p>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-success/10 text-success">
                        Active
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Modals and Forms omitted for brevity but should be implemented --}}

@endsection
