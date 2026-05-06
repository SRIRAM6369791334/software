@extends('layouts.app')
@section('title', 'Record Mortality')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('inventory.mortalities.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-600 transition-all">←</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Record Daily Mortality</h1>
            <p class="text-sm text-gray-500 mt-1">Deduct dead birds from active flock counts</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-200 shadow-2xl overflow-hidden">
        <form action="{{ route('inventory.mortalities.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="space-y-8">
                {{-- Date --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">1. Date of Occurrence</label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900">
                </div>

                {{-- Batch --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">2. Target Batch (Flock)</label>
                    <select name="batch_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900">
                        <option value="">Select Batch...</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_code }} - {{ $batch->breed }} (Current: {{ $batch->current_count }} birds)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Count --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">3. Mortality Count (Birds)</label>
                        <input type="number" name="count" required min="1" placeholder="Enter number..."
                               class="w-full px-5 py-4 bg-red-50 border border-red-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-black text-3xl text-red-600">
                    </div>

                    {{-- Reason --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">4. Primary Reason</label>
                        <select name="reason" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900">
                            <option value="">Select Reason...</option>
                            <option value="Disease">Disease / Outbreak</option>
                            <option value="Heat Stress">Heat Stress</option>
                            <option value="Cold Stress">Cold Stress</option>
                            <option value="Cannibalism">Pecking / Cannibalism</option>
                            <option value="Smothering">Smothering (Piling)</option>
                            <option value="Natural">Natural / Weakness</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">5. Detailed Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="Additional details about the event..."
                              class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all text-sm font-medium"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full mt-10 py-5 bg-red-600 text-white font-black rounded-2xl hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">
                Confirm & Record Deaths 🩸
            </button>
        </form>
    </div>

    {{-- Danger Zone Note --}}
    <div class="mt-8 p-6 bg-amber-50 border border-amber-100 rounded-3xl text-amber-800 flex gap-6 items-center">
        <div class="w-16 h-16 shrink-0 bg-amber-100 rounded-2xl flex items-center justify-center text-3xl">⚠️</div>
        <div>
            <h4 class="font-bold text-lg mb-1">Batch Count Impact</h4>
            <p class="text-amber-700/80 text-sm leading-relaxed">This action will **permanently subtract** the count from the active flock. Accuracy is critical for calculating survival rates and feeding requirements.</p>
        </div>
    </div>
</div>
@endsection
