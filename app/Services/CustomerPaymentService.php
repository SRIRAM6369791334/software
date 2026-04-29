<?php

namespace App\Services;

use App\Models\CustomerPayment;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerPaymentService
{
    public function paginated(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return CustomerPayment::with('customer')
            ->search($query)
            ->latest('date')
            ->paginate($perPage);
    }

    public function record(array $data): CustomerPayment
    {
        $payment = CustomerPayment::create($data);

        // Update customer balance
        $customer = Customer::findOrFail($data['customer_id']);
        $delta = match($data['payment_type']) {
            'Advance' => -abs($data['amount']),
            default   => -abs($data['amount']),
        };
        $customer->decrement('balance', abs($data['amount']));

        $payment->balance_after = $customer->fresh()->balance;
        $payment->save();

        return $payment;
    }

    public function allForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return CustomerPayment::with('customer')->orderByDesc('date')->get();
    }
}
