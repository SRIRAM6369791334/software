<?php

namespace Tests\Feature\Masters;

use App\Models\Customer;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
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

    public function test_index_displays_customers()
    {
        Customer::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('masters.customers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('customers');
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->admin)->get(route('masters.customers.create'));

        $response->assertStatus(200);
        $response->assertViewHas('routes');
    }

    public function test_store_creates_customer_and_redirects()
    {
        $route = Route::forceCreate(['route_name' => 'Test Route']);
        
        $data = [
            'name' => 'John Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'gst_number' => 'GSTIN1234',
            'route_id' => $route->id,
            'type' => 'Retail',
        ];

        $response = $this->actingAs($this->admin)->post(route('masters.customers.store'), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['name' => 'John Doe']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post(route('masters.customers.store'), []);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'phone', 'type']);
    }

    public function test_edit_displays_form()
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('masters.customers.edit', $customer));

        $response->assertStatus(200);
        $response->assertViewHas('customer');
        $response->assertViewHas('routes');
    }

    public function test_update_modifies_customer_and_redirects()
    {
        $customer = Customer::factory()->create();
        $data = [
            'name' => 'Jane Doe',
            'phone' => '0987654321',
            'type' => 'Wholesale',
        ];

        $response = $this->actingAs($this->admin)->put(route('masters.customers.update', $customer), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Jane Doe']);
    }

    public function test_show_displays_customer_details()
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('masters.customers.show', $customer));

        $response->assertStatus(200);
        $response->assertViewHas('customer');
    }

    public function test_destroy_deletes_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('masters.customers.destroy', $customer));
        
        $response->assertRedirect(route('masters.customers.index'));
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
