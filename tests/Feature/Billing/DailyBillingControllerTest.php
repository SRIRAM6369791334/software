<?php

namespace Tests\Feature\Billing;

use App\Models\Customer;
use App\Models\DailyBill;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyBillingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->actingAs($this->createAdmin()); // Admin role allows accountant access
    }

    public function test_index_displays_daily_bills()
    {
        $bill = DailyBill::factory()->create();

        $response = $this->get(route('billing.daily.index'));

        $response->assertStatus(200);
        $response->assertViewHas('bills');
        $response->assertViewHas('customers');
        $response->assertViewHas('items');
        $response->assertSee($bill->invoice_number);
    }

    public function test_store_creates_daily_bill()
    {
        $customer = Customer::factory()->create();
        $item = Item::factory()->create(['status' => 'Active']);

        // Seed stock for the item to prevent Insufficient stock error
        app(\App\Services\StockService::class)->recordIn([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'quantity' => 100,
            'rate' => 10,
            'date' => now()->toDateString(),
        ]);

        $payload = [
            'customer_id' => $customer->id,
            'date' => now()->toDateString(),
            'status' => 'Paid',
            'payment_mode' => 'Cash',
            'gst_percentage' => 18,
            'items' => [
                [
                    'name' => $item->name,
                    'qty' => 10,
                    'rate' => 100,
                    'unit' => 'kg',
                ],
            ],
        ];

        $response = $this->post(route('billing.daily.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('daily_bills', [
            'customer_id' => $customer->id,
            'status' => 'Paid',
            'amount' => 1000,
            'gst_percentage' => 18,
        ]);

        $this->assertDatabaseHas('daily_bill_items', [
            'item_name' => $item->name,
            'quantity_kg' => 10,
            'rate_per_kg' => 100,
            'total_amount' => 1180, // 1000 + 18% GST
        ]);
    }

    public function test_gst_view_displays_gst_report()
    {
        $bill = DailyBill::factory()->create();

        $response = $this->get(route('billing.daily.gst'));

        $response->assertStatus(200);
        $response->assertViewHas('bills');
        $response->assertSee($bill->invoice_number);
    }

    public function test_export_downloads_csv()
    {
        DailyBill::factory()->count(3)->create();

        $response = $this->get(route('billing.daily.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=daily-billing.csv');
    }

    public function test_invoice_displays_bill_details()
    {
        $bill = DailyBill::factory()->create();

        $response = $this->get(route('billing.daily.invoice', $bill));

        $response->assertStatus(200);
        $response->assertViewHas('bill');
        $response->assertSee($bill->invoice_number);
    }

    public function test_pdf_downloads_invoice()
    {
        $bill = DailyBill::factory()->create();

        $response = $this->get(route('billing.daily.pdf', $bill));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
