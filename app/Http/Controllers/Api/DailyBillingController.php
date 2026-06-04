<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GSTCalculator;
use App\Models\Customer;
use App\Models\DailyBill;
use App\Models\Item;
use App\Services\InvoiceNumberService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DailyBillingController extends BaseApiController
{
    /**
     * Get a paginated list of daily bills.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $perPage = min((int)$request->input('per_page', 15), 100);

        $bills = DailyBill::with(['customer', 'items'])
            ->search($search)
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse([
            'bills'      => $bills->items(),
            'pagination' => [
                'current_page' => $bills->currentPage(),
                'last_page'    => $bills->lastPage(),
                'per_page'     => $bills->perPage(),
                'total'        => $bills->total(),
            ]
        ], 'Daily bills retrieved successfully');
    }

    /**
     * Store a new daily bill.
     */
    public function store(Request $request, InvoiceNumberService $invoiceService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id'    => 'required|exists:customers,id',
            'date'           => 'required|date|before_or_equal:today',
            'status'         => 'required|in:Generated,Pending,Paid',
            'gst_percentage' => 'required|numeric|min:0|max:28',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string|max:255',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'items.*.rate'   => 'required|numeric|min:0.01',
            'items.*.unit'   => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors()->toArray(), 422);
        }

        try {
            $bill = DB::transaction(function () use ($request, $invoiceService) {
                $itemsData = $request->input('items');
                $gstPercent = $request->input('gst_percentage');
                
                $subtotal = 0;
                foreach ($itemsData as $item) {
                    $subtotal += $item['qty'] * $item['rate'];
                }

                $gstData = GSTCalculator::calculate($subtotal, $gstPercent);
                
                $bill = DailyBill::create([
                    'customer_id'    => $request->input('customer_id'),
                    'date'           => $request->input('date'),
                    'invoice_no'     => $invoiceService->generateUnique('INV-D', 'daily_bills'),
                    'amount'         => $subtotal,
                    'gst_percentage' => $gstPercent,
                    'gst_amount'     => $gstData['total_gst'],
                    'net_amount'     => $gstData['net_amount'],
                    'status'         => $request->input('status'),
                    'payment_mode'   => 'Cash', // Default
                ]);

                foreach ($itemsData as $item) {
                    $base = $item['qty'] * $item['rate'];
                    $tax = round($base * $gstPercent / 100, 2);
                    
                    $billItem = $bill->items()->create([
                        'item_name'    => $item['name'],
                        'quantity_kg'  => $item['qty'],
                        'rate_per_kg'  => $item['rate'],
                        'tax_amount'   => $tax,
                        'total_amount' => $base + $tax,
                        'unit'         => $item['unit'] ?? 'kg',
                    ]);

                    // Auto-trigger stock movement
                    app(StockService::class)->recordOut([
                        'item_name'      => $billItem->item_name,
                        'quantity'       => $billItem->quantity_kg,
                        'rate'           => $billItem->rate_per_kg,
                        'reference_type' => DailyBill::class,
                        'reference_id'   => $bill->id,
                        'date'           => $bill->date,
                        'created_by'     => auth()->id() ?? 1,
                    ]);
                }

                return $bill->load(['customer', 'items']);
            });

            return $this->sendResponse($bill, 'Daily bill created successfully', 201);

        } catch (\Exception $e) {
            return $this->sendError('Could not create bill', ['exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Get specific daily bill details.
     */
    public function show(DailyBill $dailyBill): JsonResponse
    {
        $dailyBill->load(['customer', 'items']);
        return $this->sendResponse($dailyBill, 'Daily bill retrieved successfully');
    }

    /**
     * Delete a daily bill.
     */
    public function destroy(DailyBill $dailyBill): JsonResponse
    {
        // Deleting the bill inside a transaction to also clean up stock movements
        try {
            DB::transaction(function () use ($dailyBill) {
                // Delete stock transactions related to this daily bill
                DB::table('stock_transactions')
                    ->where('reference_type', DailyBill::class)
                    ->where('reference_id', $dailyBill->id)
                    ->delete();

                // Delete the items
                $dailyBill->items()->delete();

                // Delete the bill itself
                $dailyBill->delete();
            });

            return $this->sendResponse([], 'Daily bill and associated stock records deleted successfully');

        } catch (\Exception $e) {
            return $this->sendError('Could not delete bill', ['exception' => $e->getMessage()], 500);
        }
    }
}
