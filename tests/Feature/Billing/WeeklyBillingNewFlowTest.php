<?php

namespace Tests\Feature\Billing;

use App\Models\Dealer;
use App\Models\WeeklyBill;
use App\Models\DealerPurchase;
use App\Models\Item;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyBillingNewFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_can_record_dealer_daily_purchase()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 0]);
        $item = Item::factory()->create(['status' => 'Active']);

        // Seed stock for the item
        app(StockService::class)->recordIn([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'quantity' => 100,
            'rate' => 10,
            'date' => '2026-06-24',
        ]);

        $payload = [
            'dealer_id' => $dealer->id,
            'date' => '2026-06-24',
            'items' => [
                [
                    'name' => $item->name,
                    'qty' => 10,
                    'rate' => 100,
                ],
            ],
        ];

        $response = $this->post(route('billing.weekly.purchase.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dealer_purchases', [
            'dealer_id' => $dealer->id,
            'amount' => 1000,
            'net_amount' => 1180, // 1000 + 18% GST
        ]);

        // Check that dealer pending amount was updated
        $this->assertEquals(1180, $dealer->fresh()->pending_amount);
    }

    public function test_can_preview_weekly_calculation()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 0]);
        $item = Item::factory()->create(['status' => 'Active']);

        // Record a purchase
        $purchase = DealerPurchase::create([
            'dealer_id' => $dealer->id,
            'date' => '2026-06-24',
            'invoice_no' => 'DPUR-TEST-1',
            'amount' => 1000,
            'gst_percentage' => 18,
            'gst_amount' => 180,
            'net_amount' => 1180,
        ]);
        $purchase->items()->create([
            'item_name' => $item->name,
            'quantity_kg' => 10,
            'rate_per_kg' => 100,
            'tax_amount' => 180,
            'total_amount' => 1180,
        ]);

        // Sync pending_amount
        $dealer->update(['pending_amount' => 1180]);

        $url = route('billing.weekly.calculate-preview') . '?' . http_build_query([
            'dealer_id' => $dealer->id,
            'period_start' => '2026-06-22',
            'period_end' => '2026-06-24',
        ]);

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'previous_outstanding' => 0.00,
            'total_purchases' => 1180.00,
            'total_payments' => 0.00,
            'net_invoice_amount' => 1180.00,
            'purchases_count' => 1,
        ]);
    }

    public function test_can_generate_weekly_bill_from_purchases()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 0]);
        $item = Item::factory()->create(['status' => 'Active']);

        $purchase = DealerPurchase::create([
            'dealer_id' => $dealer->id,
            'date' => '2026-06-24',
            'invoice_no' => 'DPUR-TEST-2',
            'amount' => 2000,
            'gst_percentage' => 18,
            'gst_amount' => 360,
            'net_amount' => 2360,
        ]);
        $purchase->items()->create([
            'item_name' => $item->name,
            'quantity_kg' => 20,
            'rate_per_kg' => 100,
            'tax_amount' => 360,
            'total_amount' => 2360,
        ]);

        $dealer->update(['pending_amount' => 2360]);

        $payload = [
            'dealer_id' => $dealer->id,
            'period_start' => '2026-06-22',
            'period_end' => '2026-06-24',
        ];

        $response = $this->post(route('billing.weekly.generate'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('weekly_bills', [
            'dealer_id' => $dealer->id,
            'net_amount' => 2360,
            'monday_payment_amount' => 1180,
            'friday_payment_amount' => 1180,
            'monday_payment_status' => 'Pending',
            'friday_payment_status' => 'Pending',
        ]);

        // Purchase should be linked to the generated bill
        $purchaseFresh = $purchase->fresh();
        $this->assertNotNull($purchaseFresh->weekly_bill_id);
    }

    public function test_can_record_split_payment()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 2000]);
        
        $bill = WeeklyBill::create([
            'dealer_id' => $dealer->id,
            'period_start' => '2026-06-15',
            'period_end' => '2026-06-21',
            'invoice_no' => 'INV-W-9999',
            'amount' => 1694.92,
            'gst_percentage' => 18,
            'gst_amount' => 305.08,
            'net_amount' => 2000,
            'status' => 'Pending',
            'payment_mode' => 'Credit',
            'monday_payment_amount' => 1000,
            'monday_payment_status' => 'Pending',
            'friday_payment_amount' => 1000,
            'friday_payment_status' => 'Pending',
        ]);

        $payload = [
            'payment_mode' => 'UPI',
            'notes' => 'Monday Payment via UPI',
        ];

        $response = $this->post(route('billing.weekly.pay-split', ['weekly' => $bill->id, 'part' => 'monday']), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('weekly_bills', [
            'id' => $bill->id,
            'monday_payment_status' => 'Paid',
            'friday_payment_status' => 'Pending',
        ]);

        // Dealer outstanding should be decremented by 1000
        $this->assertEquals(1000, $dealer->fresh()->pending_amount);

        // Dealer payment record should be created
        $this->assertDatabaseHas('dealer_payments', [
            'dealer_id' => $dealer->id,
            'amount' => 1000,
            'payment_mode' => 'UPI',
        ]);
    }
}
