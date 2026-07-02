<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DayLoadBillingController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->input('date', today()->format('Y-m-d'));
        $search = $request->input('search');

        $entries = DayLoadEntry::with(['batch', 'vendor', 'dealer'])
            ->whereHas('batch', fn ($query) => $query->whereDate('billing_date', $date))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested
                        ->whereHas('vendor', fn ($q) => $q->where('firm_name', 'like', "%{$search}%"))
                        ->orWhereHas('dealer', fn ($q) => $q->where('firm_name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15);

        $batch = DayLoadBatch::whereDate('billing_date', $date)->first();
        $vendors = Vendor::orderBy('firm_name')->get();
        $dealers = Dealer::orderBy('firm_name')->get();

        return view('billing.day-load.index', compact('entries', 'batch', 'vendors', 'dealers', 'date', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'billing_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'dealer_id' => 'required|exists:dealers,id',
            'paper_rate' => 'required|numeric|min:0',
            'billing_rate' => 'required|numeric|min:0',
            'customer_rate' => 'required|numeric|min:0',
            'no_of_boxes' => 'required|integer|min:1',
            'box_weight' => 'required|numeric|min:0',
            'empty_weight' => 'required|numeric|min:0',
            'farm_weight' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $batch = DayLoadBatch::firstOrCreate(
                ['billing_date' => $validated['billing_date']],
                ['status' => 'Open']
            );

            DayLoadEntry::create([
                'batch_id' => $batch->id,
                'vendor_id' => $validated['vendor_id'],
                'dealer_id' => $validated['dealer_id'],
                'paper_rate' => $validated['paper_rate'],
                'billing_rate' => $validated['billing_rate'],
                'customer_rate' => $validated['customer_rate'],
                'no_of_boxes' => $validated['no_of_boxes'],
                'box_weight' => $validated['box_weight'],
                'empty_weight' => $validated['empty_weight'],
                'farm_weight' => $validated['farm_weight'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $this->refreshBatchTotals($batch);
        });

        return back()->with('success', 'Daily load entry recorded successfully.');
    }

    private function refreshBatchTotals(DayLoadBatch $batch): void
    {
        $totals = $batch->entries()
            ->where('status', '!=', 'Cancelled')
            ->selectRaw('
                COALESCE(SUM(no_of_boxes), 0) as total_boxes,
                COALESCE(SUM(box_weight), 0) as total_box_weight,
                COALESCE(SUM(empty_weight), 0) as total_empty_weight,
                COALESCE(SUM(bird_weight), 0) as total_bird_weight,
                COALESCE(SUM(farm_weight), 0) as total_farm_weight,
                COALESCE(SUM(loss_weight), 0) as total_loss_weight
            ')
            ->first();

        $batch->update([
            'total_boxes' => $totals->total_boxes,
            'total_box_weight' => $totals->total_box_weight,
            'total_empty_weight' => $totals->total_empty_weight,
            'total_bird_weight' => $totals->total_bird_weight,
            'total_farm_weight' => $totals->total_farm_weight,
            'total_loss_weight' => $totals->total_loss_weight,
        ]);
    }
}
