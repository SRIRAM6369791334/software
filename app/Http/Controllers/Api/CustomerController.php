<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Masters\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends BaseApiController
{
    public function __construct(private CustomerService $service) {}

    /**
     * Get a paginated list of customers.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int)$request->input('per_page', 15), 100);

        if ($request->boolean('all') || $request->input('all') === '1') {
            $customers = \Illuminate\Support\Facades\Cache::remember('masters.customers.all', now()->addMinutes(30), function () {
                return Customer::with('routeRelation')->orderBy('name')->get();
            });

            return $this->sendResponse(CustomerResource::collection($customers), 'Customers retrieved successfully');
        }

        $search = $request->input('search');

        if (empty($search) && $perPage === 15) {
            $customers = \Illuminate\Support\Facades\Cache::remember('masters.customers.paginated.default', now()->addMinutes(30), function () {
                return Customer::with('routeRelation')
                    ->orderBy('name')
                    ->paginate(15);
            });
        } else {
            $customers = Customer::with('routeRelation')
                ->search($search)
                ->orderBy('name')
                ->paginate($perPage);
        }

        return $this->sendResponse([
            'customers' => CustomerResource::collection($customers),
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page'    => $customers->lastPage(),
                'per_page'     => $customers->perPage(),
                'total'        => $customers->total(),
            ]
        ], 'Customers retrieved successfully');
    }

    /**
     * Store a new customer.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->service->create($request->validated());
        return $this->sendResponse(new CustomerResource($customer), 'Customer added successfully', 201);
    }

    /**
     * Display the specified customer's details and dynamic stats.
     */
    public function show(Customer $customer): JsonResponse
    {
        $customer->loadCount(['weeklyBills', 'dailyBills', 'payments'])
                 ->loadSum('payments', 'amount');

        $latestWeeklyBill = $customer->weeklyBills()->latest()->first();
        $latestDailyBill = $customer->dailyBills()->latest()->first();

        $latestBill = null;
        if ($latestWeeklyBill && $latestDailyBill) {
            $latestBill = $latestWeeklyBill->period_end > $latestDailyBill->date ? $latestWeeklyBill : $latestDailyBill;
        } else {
            $latestBill = $latestWeeklyBill ?: $latestDailyBill;
        }

        $latestPayment = $customer->payments()->latest()->first();

        // Fetch top retail products bought
        $topRetailProducts = \App\Models\DailyBillItem::whereIn('daily_bill_id', $customer->dailyBills()->pluck('id'))
            ->select('item_name', \DB::raw('SUM(quantity_kg) as total_qty'), \DB::raw('COUNT(*) as times_bought'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Aggregate top wholesale products bought from weekly items
        $topWholesaleProducts = \App\Models\WeeklyBillItem::whereIn('weekly_bill_id', $customer->weeklyBills()->pluck('id'))
            ->select('item_name', \DB::raw('SUM(quantity_kg) as total_qty'), \DB::raw('COUNT(*) as times_bought'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return $this->sendResponse([
            'customer'              => new CustomerResource($customer),
            'stats'                 => [
                'payments_sum_amount'  => (float) $customer->payments_sum_amount,
                'weekly_bills_count'   => $customer->weekly_bills_count,
                'daily_bills_count'    => $customer->daily_bills_count,
                'payments_count'       => $customer->payments_count,
            ],
            'latest_bill'           => $latestBill,
            'latest_weekly_bill'    => $latestWeeklyBill,
            'latest_daily_bill'     => $latestDailyBill,
            'latest_payment'        => $latestPayment,
            'top_retail_products'   => $topRetailProducts,
            'top_wholesale_products'=> $topWholesaleProducts,
        ], 'Customer details retrieved successfully');
    }

    /**
     * Update the specified customer.
     */
    public function update(StoreCustomerRequest $request, Customer $customer): JsonResponse
    {
        $updatedCustomer = $this->service->update($customer, $request->validated());
        return $this->sendResponse(new CustomerResource($updatedCustomer), 'Customer updated successfully');
    }

    /**
     * Delete the specified customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->service->delete($customer);
        return $this->sendResponse([], 'Customer deleted successfully');
    }

    /**
     * Get billing history for a customer.
     */
    public function billingHistory(Customer $customer): JsonResponse
    {
        $totalWeeklyBilled = $customer->weeklyBills()->sum('amount');
        $totalDailyBilled = $customer->dailyBills()->sum('amount');
        $totalBilled = $totalWeeklyBilled + $totalDailyBilled;

        $weeklyBills = $customer->weeklyBills()->latest()->paginate(10, ['*'], 'weekly_page');
        $dailyBills = $customer->dailyBills()->with('items')->latest()->paginate(10, ['*'], 'daily_page');

        return $this->sendResponse([
            'customer'            => new CustomerResource($customer),
            'total_billed'        => (float) $totalBilled,
            'total_weekly_billed' => (float) $totalWeeklyBilled,
            'total_daily_billed'  => (float) $totalDailyBilled,
            'weekly_bills'        => $weeklyBills,
            'daily_bills'         => $dailyBills,
        ], 'Billing history retrieved successfully');
    }

    /**
     * Get payment history for a customer.
     */
    public function paymentHistory(Customer $customer): JsonResponse
    {
        $totalPaid = $customer->payments()->sum('amount');
        $payments = $customer->payments()->latest()->paginate(15);

        return $this->sendResponse([
            'customer'   => new CustomerResource($customer),
            'total_paid' => (float) $totalPaid,
            'payments'   => $payments,
        ], 'Payment history retrieved successfully');
    }
}
