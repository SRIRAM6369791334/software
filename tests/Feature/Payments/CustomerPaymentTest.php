<?php

namespace Tests\Feature\Payments;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_customer_payment_index_loads()
    {
        $response = $this->get('/payments/customers');
        $response->assertStatus(200);
        $response->assertViewIs('payments.customers');
    }

    public function test_customer_payment_create_loads()
    {
        $response = $this->get('/payments/customers/create');
        $response->assertStatus(200);
        $response->assertViewIs('payments.customers.create');
    }

    public function test_customer_payment_store_creates_record_and_updates_ledger()
    {
        $customer = Customer::factory()->create(['balance' => 5000]);

        $response = $this->post('/payments/customers', [
            'customer_id' => $customer->id,
            'amount' => 1500,
            'date' => today()->toDateString(),
            'payment_mode' => 'Cash',
            'payment_type' => 'Regular',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customer_payments', [
            'customer_id' => $customer->id,
            'amount' => 1500,
            'balance_after' => 3500,
        ]);

        $customer->refresh();
        $this->assertEquals(3500, $customer->balance);
    }

    public function test_customer_payment_store_advance_updates_ledger()
    {
        $customer = Customer::factory()->create(['balance' => 2000]);

        $response = $this->post('/payments/customers', [
            'customer_id' => $customer->id,
            'amount' => 3000,
            'date' => today()->toDateString(),
            'payment_mode' => 'Bank Transfer',
            'payment_type' => 'Opening',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('customer_payments', [
            'customer_id' => $customer->id,
            'amount' => 3000,
            'payment_type' => 'Opening',
            'balance_after' => -1000,
        ]);

        $customer->refresh();
        $this->assertEquals(-1000, $customer->balance);
    }

    public function test_customer_payment_requires_valid_data()
    {
        $response = $this->post('/payments/customers', []);

        $response->assertSessionHasErrors(['customer_id', 'amount', 'date', 'payment_mode', 'payment_type']);
    }

    public function test_customer_payment_export()
    {
        $customer = Customer::factory()->create(['name' => 'Test Customer']);
        CustomerPayment::create([
            'customer_id' => $customer->id,
            'amount' => 1000,
            'date' => today()->toDateString(),
            'payment_mode' => 'Cash',
            'payment_type' => 'Regular',
            'balance_after' => 0
        ]);

        $response = $this->get('/payments/customers/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=customer-payments.csv');
    }
}
