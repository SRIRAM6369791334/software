<?php

namespace Tests\Feature\Masters;

use App\Models\Customer;
use App\Models\Route;
use App\Models\DailyBill;
use App\Models\DailyBillItem;
use App\Models\WeeklyBill;
use App\Models\WeeklyBillItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerMasterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_customer_directory_loads_successfully()
    {
        $response = $this->get('/masters/customers');
        $response->assertStatus(200);
        $response->assertSee('Customer master');
    }

    public function test_customer_details_view_loads_successfully_with_aggregated_products()
    {
        $customer = Customer::factory()->create([
            'name' => 'Adarsh Poultry Mart',
            'type' => 'Wholesale'
        ]);

        // Create daily bills and items
        $dailyBill = DailyBill::factory()->create([
            'customer_id' => $customer->id,
        ]);
        DailyBillItem::create([
            'daily_bill_id' => $dailyBill->id,
            'item_name' => 'Broiler Chicken (Retail)',
            'quantity_kg' => 50.00,
            'rate_per_kg' => 120.00,
            'tax_amount' => 0.00,
            'total_amount' => 6000.00,
        ]);

        // Create weekly bills and items
        $weeklyBill = WeeklyBill::factory()->create([
            'customer_id' => $customer->id,
        ]);
        WeeklyBillItem::create([
            'weekly_bill_id' => $weeklyBill->id,
            'item_name' => 'Layer Birds (Wholesale)',
            'quantity_kg' => 100.00,
            'rate_per_kg' => 150.00,
            'tax_amount' => 0.00,
            'total_amount' => 15000.00,
        ]);

        $response = $this->get("/masters/customers/{$customer->id}");
        $response->assertStatus(200);
        $response->assertSee('Adarsh Poultry Mart');
        $response->assertSee('Broiler Chicken (Retail)');
        $response->assertSee('Layer Birds (Wholesale)');
    }

    public function test_customer_create_form_view_loads()
    {
        $response = $this->get('/masters/customers/create');
        $response->assertStatus(200);
        $response->assertSee('Add New Customer');
    }

    public function test_customer_edit_form_view_loads()
    {
        $customer = Customer::factory()->create();

        $response = $this->get("/masters/customers/{$customer->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Customer:');
    }

    public function test_customer_can_be_stored()
    {
        $route = Route::create(['route_name' => 'South Route']);

        $data = [
            'name' => 'Test Customer',
            'phone' => '9876543210',
            'address' => 'Test Address',
            'gst_number' => '33AAAAA1111A1Z1',
            'route_id' => $route->id,
            'type' => 'Retail',
        ];

        $response = $this->post('/masters/customers', $data);
        $response->assertStatus(302); // Redirect back

        $this->assertDatabaseHas('customers', [
            'name' => 'Test Customer',
            'phone' => '9876543210',
            'address' => 'Test Address',
            'type' => 'Retail',
        ]);
    }

    public function test_customer_can_be_updated()
    {
        $customer = Customer::factory()->create([
            'name' => 'Old Name',
            'phone' => '9999999999',
            'type' => 'Retail'
        ]);

        $data = [
            'name' => 'Updated Name',
            'phone' => '8888888888',
            'address' => 'New Address',
            'gst_number' => '33BBBBB2222B2Z2',
            'route_id' => null,
            'type' => 'Wholesale',
        ];

        $response = $this->put("/masters/customers/{$customer->id}", $data);
        $response->assertStatus(302);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Name',
            'phone' => '8888888888',
            'type' => 'Wholesale',
        ]);
    }

    public function test_customer_can_be_deleted()
    {
        $customer = Customer::factory()->create();

        $response = $this->delete("/masters/customers/{$customer->id}");
        $response->assertStatus(302);
        $response->assertRedirect('/masters/customers');

        // Since soft deletes are used, check that it's soft deleted
        $this->assertSoftDeleted('customers', [
            'id' => $customer->id
        ]);
    }
}
