<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\DailyBillItem;
use App\Models\WeeklyBillItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function search(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Customer::search($query)->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }

    public function find(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Customer::orderBy('name')->get();
    }

    /**
     * Load full customer detail stats and purchase analytics.
     */
    public function getDetails(Customer $customer): array
    {
        $customer->loadCount(['weeklyBills', 'dailyBills', 'payments'])
                 ->loadSum('payments', 'amount');

        $latestWeeklyBill = $customer->weeklyBills()->latest()->first();
        $latestDailyBill  = $customer->dailyBills()->latest()->first();

        $latestBill = match (true) {
            $latestWeeklyBill && $latestDailyBill => $latestWeeklyBill->period_end > $latestDailyBill->date
                ? $latestWeeklyBill : $latestDailyBill,
            default => $latestWeeklyBill ?: $latestDailyBill,
        };

        $dailyBillIds    = $customer->dailyBills()->pluck('id');
        $weeklyBillIds   = $customer->weeklyBills()->pluck('id');

        $topRetailProducts = DailyBillItem::whereIn('daily_bill_id', $dailyBillIds)
            ->select('item_name', DB::raw('SUM(quantity_kg) as total_qty'), DB::raw('COUNT(*) as times_bought'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topWholesaleProducts = WeeklyBillItem::whereIn('weekly_bill_id', $weeklyBillIds)
            ->select('item_name', DB::raw('SUM(quantity_kg) as total_qty'), DB::raw('COUNT(*) as times_bought'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return [
            'stats' => [
                'payments_sum_amount' => (float) $customer->payments_sum_amount,
                'weekly_bills_count'  => $customer->weekly_bills_count,
                'daily_bills_count'   => $customer->daily_bills_count,
                'payments_count'      => $customer->payments_count,
            ],
            'latest_bill'             => $latestBill,
            'latest_weekly_bill'      => $latestWeeklyBill,
            'latest_daily_bill'       => $latestDailyBill,
            'latest_payment'          => $customer->payments()->latest()->first(),
            'top_retail_products'     => $topRetailProducts,
            'top_wholesale_products'  => $topWholesaleProducts,
        ];
    }

    /**
     * Get paginated billing history for a customer.
     */
    public function getBillingHistory(Customer $customer, int $perPage = 10): array
    {
        $totalWeeklyBilled = $customer->weeklyBills()->sum('amount');
        $totalDailyBilled  = $customer->dailyBills()->sum('amount');

        return [
            'total_billed'        => (float) ($totalWeeklyBilled + $totalDailyBilled),
            'total_weekly_billed' => (float) $totalWeeklyBilled,
            'total_daily_billed'  => (float) $totalDailyBilled,
            'weekly_bills'        => $customer->weeklyBills()->latest()->paginate($perPage, ['*'], 'weekly_page'),
            'daily_bills'         => $customer->dailyBills()->with('items')->latest()->paginate($perPage, ['*'], 'daily_page'),
        ];
    }

    /**
     * Get paginated payment history for a customer.
     */
    public function getPaymentHistory(Customer $customer, int $perPage = 15): array
    {
        return [
            'total_paid' => (float) $customer->payments()->sum('amount'),
            'payments'   => $customer->payments()->latest()->paginate($perPage),
        ];
    }
}
