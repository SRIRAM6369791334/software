<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
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
}
