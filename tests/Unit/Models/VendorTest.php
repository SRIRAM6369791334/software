<?php

namespace Tests\Unit\Models;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes()
    {
        $vendor = new Vendor();
        $this->assertEquals([
            'firm_name', 'gst_number', 'location', 'contact_person', 'phone', 'route', 'notes'
        ], $vendor->getFillable());
    }

    public function test_search_scope_filters_correctly()
    {
        Vendor::factory()->create(['firm_name' => 'Acme Corp', 'phone' => '1112223334']);
        Vendor::factory()->create(['firm_name' => 'Globex', 'contact_person' => 'Homer Simpson']);

        $this->assertEquals(1, Vendor::search('Acme')->count());
        $this->assertEquals(1, Vendor::search('Homer')->count());
        $this->assertEquals(2, Vendor::search('')->count());
    }
}
