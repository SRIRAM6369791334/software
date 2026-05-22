<?php

namespace Tests\Feature\Masters;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorMasterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_vendor_directory_loads_successfully()
    {
        $response = $this->get('/masters/vendors');
        $response->assertStatus(200);
        $response->assertSee('Vendor Master');
    }

    public function test_vendor_details_view_loads_successfully()
    {
        $vendor = Vendor::factory()->create([
            'firm_name' => 'Apex Feed Corp'
        ]);

        $response = $this->get("/masters/vendors/{$vendor->id}");
        $response->assertStatus(200);
        $response->assertSee('Apex Feed Corp');
    }

    public function test_vendor_purchase_history_loads_successfully()
    {
        $vendor = Vendor::factory()->create([
            'firm_name' => 'Apex Feed Corp'
        ]);

        $response = $this->get("/masters/vendors/{$vendor->id}/purchase-history");
        $response->assertStatus(200);
        $response->assertSee('Apex Feed Corp');
        $response->assertSee('Full Purchase History');
    }

    public function test_vendor_create_form_view_loads()
    {
        $response = $this->get('/masters/vendors/create');
        $response->assertStatus(200);
        $response->assertSee('Register New Vendor');
    }

    public function test_vendor_edit_form_view_loads()
    {
        $vendor = Vendor::factory()->create();

        $response = $this->get("/masters/vendors/{$vendor->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Vendor Profile');
    }
}
