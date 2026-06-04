<?php

namespace App\Observers;

use App\Models\Item;
use Illuminate\Support\Facades\Cache;

class ItemObserver
{
    private function clearCache(): void
    {
        Cache::forget('masters.items.all');
        Cache::forget('masters.items.paginated.default');
    }

    public function created(Item $item): void
    {
        $this->clearCache();
    }

    public function saved(Item $item): void
    {
        $this->clearCache();
    }

    public function updated(Item $item): void
    {
        $this->clearCache();
    }

    public function deleted(Item $item): void
    {
        $this->clearCache();
    }

    public function restored(Item $item): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Item $item): void
    {
        $this->clearCache();
    }
}
