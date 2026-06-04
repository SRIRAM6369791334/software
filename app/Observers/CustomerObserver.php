<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Facades\Cache;

class CustomerObserver
{
    private function clearCache(): void
    {
        Cache::forget('masters.customers.all');
        Cache::forget('masters.customers.paginated.default');
    }

    public function created(Customer $customer): void
    {
        $this->clearCache();
    }

    public function saved(Customer $customer): void
    {
        $this->clearCache();
    }

    public function updated(Customer $customer): void
    {
        $this->clearCache();
    }

    public function deleted(Customer $customer): void
    {
        $this->clearCache();
    }

    public function restored(Customer $customer): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Customer $customer): void
    {
        $this->clearCache();
    }
}
