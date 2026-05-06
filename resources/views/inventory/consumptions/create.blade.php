@extends('layouts.app')
@section('title', 'Record Usage')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('inventory.consumptions.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-emerald-600 transition-all">←</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Record Daily Consumption</h1>
            <p class="text-sm text-gray-500 mt-1">Deduct feed/medicine from stock and link to a batch</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-200 shadow-2xl overflow-hidden">
        <form action="{{ route('inventory.consumptions.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {{-- Date --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">1. Date of Usage</label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-gray-900">
                </div>

                {{-- Batch --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">2. Target Batch (Flock)</label>
                    <select name="batch_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-gray-900">
                        <option value="">Select Batch...</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_code }} ({{ $batch->breed }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Item --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">3. Item (Feed/Medicine)</label>
                    <select name="item_id" required onchange="updateStockDisplay(this)" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-gray-900">
                        <option value="">Select Item...</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} ({{ $item->code }})
                            </option>
                        @endforeach
                    </select>
                    <div id="stock-hint" class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight mt-1 hidden">
                        Available Stock: <span id="available-qty">0.00</span>
                    </div>
                </div>

                {{-- Warehouse --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">4. Source Warehouse</label>
                    <select name="warehouse_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-gray-900">
                        <option value="">Select Godown/Shed...</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id', $loop->first ? $wh->id : '') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pt-8 border-t border-gray-100">
                {{-- Quantity --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">5. Quantity Consumed</label>
                    <div class="relative">
                        <input type="number" name="quantity" step="0.01" required placeholder="0.00"
                               class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-2xl text-emerald-700">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-white px-2 py-1 rounded-lg border border-gray-100">Unit</span>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">6. Remarks / Notes</label>
                    <textarea name="remarks" rows="2" placeholder="e.g. Extra feed due to cold weather..."
                              class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-sm font-medium"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
                Confirm & Deduct Stock 🚀
            </button>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="mt-8 p-6 bg-blue-600 rounded-3xl text-white shadow-xl shadow-blue-600/20 flex gap-6 items-center">
        <div class="w-16 h-16 shrink-0 bg-white/10 rounded-2xl flex items-center justify-center text-3xl">💡</div>
        <div>
            <h4 class="font-bold text-lg mb-1">Stock Availability Check</h4>
            <p class="text-blue-100 text-sm leading-relaxed">The system will automatically prevent saving if the quantity exceeds current stock. Recording usage daily ensures accurate **FCR tracking** and batch performance analysis.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStockDisplay(select) {
    // Note: To make this truly reactive, we could add an AJAX call here 
    // to fetch the real-time stock of the selected item.
    // For now, it serves as a visual placeholder.
    const hint = document.getElementById('stock-hint');
    if (select.value) {
        hint.classList.remove('hidden');
    } else {
        hint.classList.add('hidden');
    }
}
</script>
@endpush
