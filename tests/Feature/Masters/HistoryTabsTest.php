<?php

namespace Tests\Feature\Masters;

use App\Models\Customer;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_customer_billing_and_payment_history_routes_load()
    {
        $customer = Customer::factory()->create();

        $response = $this->get("/masters/customers/{$customer->id}/billing-history");
        $response->assertStatus(200);

        $response = $this->get("/masters/customers/{$customer->id}/payment-history");
        $response->assertStatus(200);
    }

    public function test_dealer_purchase_history_and_outstanding_report_routes_load()
    {
        $dealer = Dealer::factory()->create();

        $response = $this->get("/masters/dealers/{$dealer->id}/purchase-history");
        $response->assertStatus(200);

        $response = $this->get("/masters/dealers/{$dealer->id}/outstanding-report");
        $response->assertStatus(200);
    }
}
