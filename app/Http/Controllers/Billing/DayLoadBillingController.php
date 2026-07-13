<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
use App\Models\EntryAdjustmentLog;
use App\Models\Vendor;
use App\Services\DayLoadBillingService;
use App\Services\DayLoadPaymentService;
use App\Services\ExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DayLoadBillingController extends Controller
{
    public function __construct(
        private DayLoadBillingService $dayLoadBillingService,
        private DayLoadPaymentService $dayLoadPaymentService,
        private ExportService $exporter,
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

        $allEntries = DayLoadEntry::with(['batch', 'vendor', 'dealer'])
            ->whereHas('batch', fn ($q) => $q->whereDate('billing_date', $date))
            ->where('status', '!=', 'Cancelled')
            ->get();

        $totalDealerIncome    = $allEntries->sum(fn($e) => $e->dealer_income);
        $totalVendorCost      = $allEntries->sum(fn($e) => $e->vendor_cost);
        $grossMargin          = round($totalDealerIncome - $totalVendorCost, 2);
        $totalDealerCollected = $allEntries->sum(fn($e) => (float) $e->dealer_collected);
        $totalVendorPaid      = $allEntries->sum(fn($e) => (float) $e->vendor_paid);
        $totalDealerDue       = round($totalDealerIncome - $totalDealerCollected, 2);
        $totalVendorDue       = round($totalVendorCost - $totalVendorPaid, 2);
        $collectionPct        = $totalDealerIncome > 0 ? round(($totalDealerCollected / $totalDealerIncome) * 100, 1) : 0;

        $lsEntriesByDealer = $allEntries->groupBy('dealer_id')->map(fn($entries) => $entries->map(fn($e) => [
            'id'               => $e->id,
            'vendor'           => $e->vendor->firm_name ?? '-',
            'dealer_income'    => (float) $e->dealer_income,
            'dealer_collected' => (float) $e->dealer_collected,
            'due'              => max(0, round($e->dealer_income - (float) $e->dealer_collected, 2)),
        ]));

        return view('billing.day-load.index', compact(
            'entries', 'batch', 'vendors', 'dealers', 'date', 'search',
            'totalDealerIncome', 'totalVendorCost', 'grossMargin',
            'totalDealerCollected', 'totalVendorPaid',
            'totalDealerDue', 'totalVendorDue', 'collectionPct',
            'lsEntriesByDealer',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'billing_date'  => 'required|date',
            'vendor_id'     => 'required|exists:vendors,id',
            'dealer_id'     => 'required|exists:dealers,id',
            'paper_rate'    => 'required|numeric|min:0',
            'billing_rate'  => 'nullable|numeric|min:0',
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

    public function recordDealerPayment(Request $request, DayLoadEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'date'              => 'required|date|before_or_equal:today',
            'payment_mode'      => 'required|in:' . implode(',', config('payments.modes')),
            'cash_amount'       => 'required|numeric|min:0',
            'bank_amount'       => 'required|numeric|min:0',
                'bank_transfer_type' => [
                    function ($attribute, $value, $fail) {
                        $bankAmount = (float) (request()->input('bank_amount') ?? 0);
                        if ($bankAmount > 0) {
                            if (blank($value)) {
                                $fail('The bank transfer type field is required when bank amount is greater than 0.');
                            } elseif (!in_array($value, ['UPI', 'Bank Transfer', 'NEFT', 'RTGS', 'IMPS', 'Cheque', 'Other'], true)) {
                                $fail('The selected bank transfer type is invalid.');
                            }
                        }
                    },
                ],
            'amount'            => 'nullable|numeric|min:0',
            'reference_number'  => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:500',
        ]);

        if ((float) $validated['cash_amount'] + (float) $validated['bank_amount'] <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        try {
            $this->dayLoadPaymentService->recordDealerPayment($entry, $validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not record payment: ' . $e->getMessage());
        }

        return back()->with('success', 'Dealer payment recorded successfully.');
    }

    public function recordVendorPayment(Request $request, DayLoadEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'date'               => 'required|date|before_or_equal:today',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'payment_mode'       => 'required|in:' . implode(',', config('payments.modes')),
            'bank_transfer_type' => 'nullable|required_if:bank_amount,>0|in:UPI,Bank Transfer,NEFT,RTGS,IMPS,Cheque,Other',
            'reference_number'   => 'nullable|string|max:100',
            'notes'              => 'nullable|string|max:500',
        ]);

        if ((float) $validated['cash_amount'] + (float) $validated['bank_amount'] <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        try {
            $this->dayLoadPaymentService->recordVendorPayment($entry, $validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not record payment: ' . $e->getMessage());
        }

        return back()->with('success', 'Vendor payment recorded successfully.');
    }

    public function recordLumpSumDealerPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_id'          => 'required|exists:dealers,id',
            'date'               => 'required|date|before_or_equal:today',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'payment_mode'       => 'required|in:' . implode(',', config('payments.modes')),
            'bank_transfer_type' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $bankAmount = (float) (request()->input('bank_amount') ?? 0);
                    if ($bankAmount > 0) {
                        if (blank($value)) {
                            $fail('The bank transfer type field is required when bank amount is greater than 0.');
                        } elseif (!in_array($value, ['UPI', 'Bank Transfer', 'NEFT', 'RTGS', 'IMPS', 'Cheque', 'Other'], true)) {
                            $fail('The selected bank transfer type is invalid.');
                        }
                    }
                },
            ],
            'allocations'        => 'required|array|min:1',
            'allocations.*'      => 'required|numeric|min:0',
            'reference_number'   => 'nullable|string|max:100',
            'notes'              => 'nullable|string|max:500',
        ]);

        $totalAmount = round((float) $validated['cash_amount'] + (float) $validated['bank_amount'], 2);
        if ($totalAmount <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        $allocSum = round(collect($validated['allocations'])->sum(fn ($v) => (float) $v), 2);
        if ($allocSum > $totalAmount) {
            return back()->with('error',
                'Total allocation (Rs ' . number_format($allocSum, 2) . ') exceeds lump-sum amount (Rs ' . number_format($totalAmount, 2) . ').'
            );
        }

        try {
            $this->dayLoadPaymentService->recordLumpSumDealerPayment($validated);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Could not record lump-sum payment: ' . $e->getMessage());
        }

        return back()->with('success', 'Lump-sum dealer payment recorded successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $date = $request->input('date', today()->format('Y-m-d'));

        $entries = DayLoadEntry::with(['vendor', 'dealer', 'batch'])
            ->whereHas('batch', fn ($q) => $q->whereDate('billing_date', $date))
            ->where('status', '!=', 'Cancelled')
            ->get();

        $rows = $entries->map(fn ($e) => [
            $e->batch->billing_date->format('Y-m-d'),
            $e->vendor->firm_name ?? '-',
            $e->dealer->firm_name ?? '-',
            $e->no_of_boxes,
            number_format((float) $e->bird_weight, 2),
            number_format((float) ($e->farm_weight ?? 0), 2),
            number_format((float) ($e->loss_weight ?? 0), 2),
            number_format((float) $e->total_weight, 2),
            $e->paper_rate,
            $e->billing_rate,
            $e->customer_rate,
            $e->status,
        ]);

        $filename = 'day-load-' . $date;
        return $this->exporter->streamCsv($filename, [
            'Date', 'Vendor', 'Dealer', 'Boxes', 'Bird Wt', 'Farm Wt', 'Loss Wt', 'Total Wt',
            'Paper Rate', 'Billing Rate', 'Customer Rate', 'Status',
        ], $rows);
    }

    public function invoice(string $date): View
    {
        $dateObj = Carbon::parse($date);
        $batch = DayLoadBatch::whereDate('billing_date', $date)->first();
        $entries = DayLoadEntry::with(['vendor', 'dealer'])
            ->whereHas('batch', fn ($q) => $q->whereDate('billing_date', $date))
            ->where('status', '!=', 'Cancelled')
            ->get();

        return view('billing.day-load.invoice', compact('dateObj', 'batch', 'entries'));
    }

    public function downloadPdf(string $date)
    {
        $dateObj = Carbon::parse($date);
        $batch = DayLoadBatch::whereDate('billing_date', $date)->first();
        $entries = DayLoadEntry::with(['vendor', 'dealer'])
            ->whereHas('batch', fn ($q) => $q->whereDate('billing_date', $date))
            ->where('status', '!=', 'Cancelled')
            ->get();

        $pdf = Pdf::loadView('billing.day-load.pdf', compact('dateObj', 'batch', 'entries'));
        return $pdf->download("day-load-{$date}.pdf");
    }

    public function vendorRatesForm(Request $request): View
    {
        $vendors = Vendor::orderBy('firm_name')->get();
        $groupedEntries = collect();
        $financialSummary = null;
        $selectedVendorId = null;

        if ($request->filled('vendor_id')) {
            $selectedVendorId = (int) $request->vendor_id;

            $entries = DayLoadEntry::with(['batch', 'vendor'])
                ->where('vendor_id', $selectedVendorId)
                ->where('status', '!=', 'Cancelled')
                ->whereHas('batch', fn($q) => $q->where('status', '!=', 'Locked'))
                ->get();

            $groupedEntries = $entries
                ->groupBy(fn($e) => $e->batch->billing_date->format('Y-m-d'))
                ->map(fn($items, $date) => [
                    'date'          => $date,
                    'count'         => $items->count(),
                    'total_weight'  => round($items->sum('bird_weight'), 2),
                    'paper_rate'    => (float) $items->first()->paper_rate,
                    'current_rate'  => (float) ($items->first()->billing_rate ?: 0),
                    'entry_ids'     => $items->pluck('id')->toArray(),
                    'batch_ids'     => $items->pluck('batch_id')->unique()->values()->toArray(),
                ])->sortKeys();

            $currentVendorCost = $entries->sum(fn($e) => $e->vendor_cost);
            $financialSummary = [
                'current_vendor_cost' => $currentVendorCost,
                'total_entries'       => $entries->count(),
                'total_weight'        => round($entries->sum('bird_weight'), 2),
            ];
        }

        return view('billing.day-load.vendor-rates', compact(
            'vendors', 'groupedEntries', 'financialSummary', 'selectedVendorId'
        ));
    }

    public function setVendorRates(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'rates'     => 'required|array',
            'rates.*'   => 'required|numeric|min:0',
            'reason'    => 'required|string|max:255',
        ]);

        $allEntries = DayLoadEntry::with('batch')
            ->where('vendor_id', $validated['vendor_id'])
            ->where('status', '!=', 'Cancelled')
            ->whereHas('batch', fn($q) => $q->where('status', '!=', 'Locked'))
            ->get()
            ->groupBy(fn($e) => $e->batch->billing_date->format('Y-m-d'));

        foreach ($validated['rates'] as $date => $rate) {
            if (!isset($allEntries[$date]) || $allEntries[$date]->isEmpty()) {
                return back()->with('error', "No active entries found for date: {$date}.");
            }
        }

        $updatedCount = 0;
        $costBefore = 0;
        $costAfter = 0;
        $statusChanges = ['Overpaid' => 0, 'Pending' => 0, 'Partial' => 0, 'Unchanged' => 0];
        $skippedLocked = 0;

        DB::transaction(function () use ($allEntries, $validated, &$updatedCount, &$costBefore, &$costAfter, &$statusChanges, &$skippedLocked) {
            $refreshedBatches = collect();

            foreach ($validated['rates'] as $date => $newRate) {
                foreach ($allEntries[$date] as $entry) {
                    $costBefore += $entry->vendor_cost;

                    $oldBillingRate = $entry->billing_rate;
                    $entry->updateQuietly(['billing_rate' => $newRate]);

                    $costAfter += $entry->vendor_cost;

                    EntryAdjustmentLog::create([
                        'entry_id'    => $entry->id,
                        'action_type' => 'Edit',
                        'old_values'  => ['billing_rate' => (float) ($oldBillingRate ?: 0)],
                        'new_values'  => ['billing_rate' => $newRate],
                        'reason'      => $validated['reason'],
                        'adjusted_by' => auth()->id(),
                    ]);

                    $oldStatus = $entry->getOriginal('vendor_payment_status');
                    $this->dayLoadPaymentService->refreshVendorPaymentStatus($entry);
                    $entry->refresh();
                    $newStatus = $entry->vendor_payment_status;
                    $statusChanges[$oldStatus === $newStatus ? 'Unchanged' : $newStatus]++;

                    if (!$refreshedBatches->has($entry->batch_id)) {
                        $refreshedBatches->put($entry->batch_id, true);
                        $this->dayLoadPaymentService->refreshBatchFinancials($entry->batch);
                    }

                    $updatedCount++;
                }
            }
        });

        $difference = round($costBefore - $costAfter, 2);

        return back()->with('success', 'Vendor final rates updated successfully.')
            ->with('update_summary', [
                'dates_updated'  => count($validated['rates']),
                'entries_updated'=> $updatedCount,
                'cost_before'    => round($costBefore, 2),
                'cost_after'     => round($costAfter, 2),
                'difference'     => $difference,
                'status_changes' => $statusChanges,
            ]);
    }
}
