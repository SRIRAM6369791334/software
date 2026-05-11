@extends('layouts.app')
@section('title', 'Vendor Details')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('masters.vendors.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Vendors</a>
        <h1 class="text-2xl font-bold text-slate-950">{{ $vendor->firm_name }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">Vendor Master Record | {{ $vendor->location ?? 'Location not set' }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('masters.vendors.edit', $vendor) }}" class="px-4 py-2 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-emerald-50 shadow-sm transition-all">Edit Vendor</a>
        <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Remove this vendor?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-lg text-sm font-bold hover:bg-red-100 transition-all">Delete</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Info --}}
    <div class="space-y-6">
        <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Business Profile</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Supplier Contact</p>
                    <p class="text-sm font-semibold text-slate-950">{{ $vendor->contact_person ?: '-' }}</p>
                    <p class="text-xs text-emerald-600 font-bold px-1">{{ $vendor->phone }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Items / Location</p>
                    <p class="text-sm text-slate-950 italic break-words">{{ $vendor->location ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">GSTIN</p>
                    <p class="text-sm font-mono text-slate-950 border-l-2 border-indigo-100 pl-2 ml-1">{{ $vendor->gst_number ?: 'Unregistered' }}</p>
                </div>
            </div>
            @if($vendor->notes)
            <div class="mt-4 p-3 bg-indigo-50/50 rounded-lg text-xs text-indigo-700">
                <p class="font-bold uppercase tracking-tight text-[9px] mb-1 opacity-60">Vendor Notes</p>
                {{ $vendor->notes }}
            </div>
            @endif
        </div>
    </div>

    {{-- Activity --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex border-b border-slate-200 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
                <a href="{{ route('masters.vendors.show', $vendor) }}" class="px-6 py-4 text-sm font-bold text-primary border-b-2 border-primary">Quick Look</a>
                <a href="{{ route('masters.vendors.purchase-history', $vendor) }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-950">Full Purchase History</a>
            </div>
            
            <div class="p-6">
                <h4 class="text-sm font-bold text-slate-950 mb-4 uppercase tracking-tight">Recent Supply Activity</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left border-b border-slate-200 text-slate-400 uppercase tracking-wider">
                                <th class="pb-2">Date</th>
                                <th class="pb-2">Item</th>
                                <th class="pb-2 text-right">Qty</th>
                                <th class="pb-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($vendor->purchases()->latest()->take(5)->get() as $purchase)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-700">{{ $purchase->date->format('d M y') }}</td>
                                    <td class="py-3 font-bold text-slate-950">{{ $purchase->item }}</td>
                                    <td class="py-3 text-right text-slate-600">{{ $purchase->quantity }} {{ $purchase->unit }}</td>
                                    <td class="py-3 text-right font-bold text-slate-950">Rs {{ number_format($purchase->total_amount, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-slate-400 italic">No supply history recorded for this vendor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    <a href="{{ route('purchases.create', ['vendor_name' => $vendor->firm_name]) }}" class="text-xs font-bold text-primary hover:underline">Record New Purchase Entry -></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
