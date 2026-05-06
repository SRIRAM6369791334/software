<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::query();

        if ($request->search) {
            $query->where('batch_code', 'like', "%{$request->search}%")
                  ->orWhere('breed', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $batches = $query->latest('placement_date')->paginate(10);

        return view('inventory.batches.index', compact('batches'));
    }

    public function create()
    {
        // Generate a default batch code like BATCH-2023-001
        $latestBatch = Batch::latest()->first();
        $nextId = $latestBatch ? $latestBatch->id + 1 : 1;
        $defaultCode = 'BATCH-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('inventory.batches.create', compact('defaultCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_code' => 'required|unique:batches,batch_code',
            'placement_date' => 'required|date',
            'initial_count' => 'required|integer|min:1',
            'breed' => 'nullable|string',
            'avg_placement_weight' => 'nullable|numeric|min:0',
        ]);

        $validated['current_count'] = $request->initial_count;
        $validated['status'] = 'Active';

        Batch::create($validated);

        return redirect()->route('inventory.batches.index')
            ->with('success', 'New batch registered successfully.');
    }

    public function show(Batch $batch)
    {
        return view('inventory.batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        return view('inventory.batches.edit', compact('batch'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'batch_code' => 'required|unique:batches,batch_code,' . $batch->id,
            'placement_date' => 'required|date',
            'initial_count' => 'required|integer|min:1',
            'current_count' => 'required|integer|min:0',
            'breed' => 'nullable|string',
            'avg_placement_weight' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Closed',
            'closed_at' => 'nullable|required_if:status,Closed|date',
        ]);

        $batch->update($validated);

        return redirect()->route('inventory.batches.index')
            ->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        // Check if batch has associated movements/stock (future-proofing)
        $batch->delete();

        return redirect()->route('inventory.batches.index')
            ->with('success', 'Batch record deleted.');
    }
}
