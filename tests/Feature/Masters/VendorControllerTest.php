<?php

namespace Tests\Feature\Masters;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
    }

    public function test_index_displays_vendors()
    {
        Vendor::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('masters.vendors.index'));

        $response->assertStatus(200);
        $response->assertViewHas('vendors');
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->admin)->get(route('masters.vendors.create'));

        $response->assertStatus(200);
    }

    public function test_store_creates_vendor_and_redirects()
    {
        $data = [
            'firm_name' => 'Vendor Corp',
            'phone' => '1234567890',
            'contact_person' => 'John Vendor',
            'gst_number' => 'GSTIN9876',
            'location' => 'City Center',
            'route' => 'Route A',
            'notes' => 'Some notes here',
        ];

        $response = $this->actingAs($this->admin)->post(route('masters.vendors.store'), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vendors', ['firm_name' => 'Vendor Corp']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post(route('masters.vendors.store'), []);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['firm_name', 'phone']);
    }

    public function test_edit_displays_form()
    {
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('masters.vendors.edit', $vendor));

        $response->assertStatus(200);
        $response->assertViewHas('vendor');
    }

    public function test_update_modifies_vendor_and_redirects()
    {
        $vendor = Vendor::factory()->create();
        $data = [
            'firm_name' => 'Updated Vendor Corp',
            'phone' => '0987654321',
        ];

        $response = $this->actingAs($this->admin)->put(route('masters.vendors.update', $vendor), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'firm_name' => 'Updated Vendor Corp']);
    }

    public function test_show_displays_vendor_details()
    {
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('masters.vendors.show', $vendor));

        $response->assertStatus(200);
        $response->assertViewHas('vendor');
    }

    public function test_destroy_deletes_vendor()
    {
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('masters.vendors.destroy', $vendor));
        
        $response->assertRedirect(route('masters.vendors.index'));
        $this->assertSoftDeleted('vendors', ['id' => $vendor->id]);
    }
}
