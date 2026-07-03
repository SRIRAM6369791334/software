<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
use App\Models\EntryAdjustmentLog;
use App\Models\Vendor;
use App\Services\DayLoadBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DayLoadBillingController extends Controller
{
    public function __construct(
        private DayLoadBillingService $dayLoadBillingService,
    ) {}

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
            'billing_date'  => 'required|date',
            'vendor_id'     => 'required|exists:vendors,id',
            'dealer_id'     => 'required|exists:dealers,id',
            'paper_rate'    => 'required|numeric|min:0',
            'billing_rate'  => 'required|numeric|min:0',
            'customer_rate' => 'required|numeric|min:0',
            'no_of_boxes'   => 'required|integer|min:1',
            'box_weight'    => 'required|numeric|min:0',
            'empty_weight'  => 'required|numeric|min:0',
            'farm_weight'   => 'nullable|numeric|min:0',
            'remarks'       => 'nullable|string|max:255',
        ]);

        $this->dayLoadBillingService->createEntry($validated);

        return back()->with('success', 'Daily load entry recorded successfully.');
    }

    public function transfer(Request $request, DayLoadEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'transfer_boxes'   => 'required|integer|min:1',
            'target_dealer_id' => 'required|exists:dealers,id',
            'target_vendor_id' => 'required|exists:vendors,id',
            'reason'           => 'required|string|max:255',
        ]);

        $validated['target_batch_id'] = $entry->batch_id;

        $this->dayLoadBillingService->transferBoxes($entry, $validated, $validated['reason']);

        return back()->with('success', "{$validated['transfer_boxes']} box(es) transferred successfully.");
    }

    public function update(Request $request, DayLoadEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'dealer_id'     => 'required|exists:dealers,id',
            'paper_rate'    => 'required|numeric|min:0',
            'billing_rate'  => 'required|numeric|min:0',
            'customer_rate' => 'required|numeric|min:0',
            'no_of_boxes'   => 'required|integer|min:1',
            'box_weight'    => 'required|numeric|min:0',
            'empty_weight'  => 'required|numeric|min:0',
            'farm_weight'   => 'nullable|numeric|min:0',
            'remarks'       => 'nullable|string|max:255',
            'reason'        => 'required|string|max:255',
        ]);

        $this->dayLoadBillingService->updateEntry($entry, $validated);

        return back()->with('success', 'Entry updated successfully.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entries'   => 'required|array',
            'entries.*.id' => 'required|exists:day_load_entries,id',
            'entries.*.farm_weight' => 'nullable|numeric|min:0',
            'entries.*.remarks' => 'nullable|string|max:255',
            'reason'    => 'required|string|max:255',
        ]);

        foreach ($validated['entries'] as $entryData) {
            $entry = DayLoadEntry::find($entryData['id']);

            if ($entry && $entry->status === 'Active') {
                $oldValues = $entry->toArray();

                $entry->update([
                    'farm_weight' => $entryData['farm_weight'] ?? null,
                    'remarks'     => $entryData['remarks'] ?? null,
                ]);

                EntryAdjustmentLog::create([
                    'entry_id'            => $entry->id,
                    'action_type'         => 'Edit',
                    'old_values'          => $oldValues,
                    'new_values'          => $entry->fresh()->toArray(),
                    'resulting_entry_id'  => null,
                    'reason'              => $validated['reason'],
                    'adjusted_by'         => auth()->id(),
                ]);

                $this->dayLoadBillingService->refreshBatchTotals($entry->batch);
            }
        }

        return back()->with('success', 'All entries updated successfully.');
    }

    public function setFarmWeight(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'batch_id'       => 'required|exists:day_load_batches,id',
            'total_farm_weight' => 'required|numeric|min:0',
            'reason'         => 'required|string|max:255',
        ]);

        $batch = DayLoadBatch::findOrFail($validated['batch_id']);
        $totalFarmWeight = (float) $validated['total_farm_weight'];

        $entries = $batch->entries()->where('status', 'Active')->get();
        $totalBirdWeight = (float) $entries->sum('bird_weight');

        if ($totalBirdWeight <= 0) {
            return back()->with('error', 'No active entries with bird weight found.');
        }

        foreach ($entries as $entry) {
            $birdWeight = (float) $entry->bird_weight;
            $proportion = $birdWeight / $totalBirdWeight;
            $distributedFarmWeight = round($totalFarmWeight * $proportion, 2);

            $oldValues = $entry->toArray();

            $entry->update([
                'farm_weight' => $distributedFarmWeight,
            ]);

            EntryAdjustmentLog::create([
                'entry_id'           => $entry->id,
                'action_type'        => 'Edit',
                'old_values'         => $oldValues,
                'new_values'         => $entry->fresh()->toArray(),
                'resulting_entry_id' => null,
                'reason'             => $validated['reason'],
                'adjusted_by'        => auth()->id(),
            ]);
        }

        $this->dayLoadBillingService->refreshBatchTotals($batch);

        return back()->with('success', 'Farm weight distributed across all entries.');
    }
}
