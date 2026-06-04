<?php

namespace Tests\Unit\Models;

use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope()
    {
        Warehouse::create(['name' => 'Active WH', 'location' => 'Loc 1', 'is_active' => 1]);
        Warehouse::create(['name' => 'Inactive WH', 'location' => 'Loc 2', 'is_active' => 0]);

        $activeWarehouses = Warehouse::active()->get();

        $this->assertCount(1, $activeWarehouses);
        $this->assertEquals('Active WH', $activeWarehouses->first()->name);
    }
}
