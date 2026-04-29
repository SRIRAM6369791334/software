<?php

namespace App\Services;

use App\Models\Dealer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DealerService
{
    public function search(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Dealer::search($query)->orderBy('firm_name')->paginate($perPage);
    }

    public function create(array $data): Dealer
    {
        return Dealer::create($data);
    }

    public function update(Dealer $dealer, array $data): Dealer
    {
        $dealer->update($data);
        return $dealer->fresh();
    }

    public function delete(Dealer $dealer): void
    {
        $dealer->delete();
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Dealer::orderBy('firm_name')->get();
    }
}
