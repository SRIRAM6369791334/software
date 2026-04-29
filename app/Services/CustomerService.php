<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
