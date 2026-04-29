<?php

namespace Tests\Feature\Payments;

use App\Models\Dealer;
use App\Models\DealerPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_dealer_payment_page_loads()
    {
        $response = $this->get('/payments/dealers');

        $response->assertStatus(200);
    }

    public function test_dealer_payment_store_creates_record()
    {
        $dealer = Dealer::factory()->create();

        $response = $this->post('/payments/dealers', [
            'dealer_id' => $dealer->id,
            'amount' => 1000,
            'date' => today()->toDateString(),
            'payment_mode' => 'Cash',
        ]);

        $this->assertDatabaseHas('dealer_payments', ['dealer_id' => $dealer->id, 'amount' => 1000]);
    }

    public function test_dealer_payment_auto_deducts_pending_amount()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 5000]);

        $this->post('/payments/dealers', [
            'dealer_id' => $dealer->id,
            'amount' => 2000,
            'date' => today()->toDateString(),
            'payment_mode' => 'Cash',
        ]);

        $dealer->refresh();
        $this->assertEquals(3000, $dealer->pending_amount);
    }

    public function test_dealer_payment_pending_amount_never_goes_negative()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 500]);

        $this->post('/payments/dealers', [
            'dealer_id' => $dealer->id,
            'amount' => 1000,
            'date' => today()->toDateString(),
            'payment_mode' => 'Cash',
        ]);

        $dealer->refresh();
        $this->assertGreaterThanOrEqual(0, $dealer->pending_amount);
    }

    public function test_dealer_payment_requires_valid_data()
    {
        $response = $this->post('/payments/dealers', []);

        $response->assertSessionHasErrors(['dealer_id', 'amount']);
    }

    public function test_dealer_payment_redirects_after_success()
    {
        $dealer = Dealer::factory()->create();

        $response = $this->post('/payments/dealers', [
            'dealer_id' => $dealer->id,
            'amount' => 500,
            'date' => today()->toDateString(),
            'payment_mode' => 'Cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
