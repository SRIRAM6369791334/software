<?php

namespace Tests\Feature\Billing;

use App\Models\Customer;
use App\Models\WeeklyBill;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyBillingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->actingAs($this->createAdmin());
    }

    public function test_index_displays_weekly_bills()
    {
        $bill = WeeklyBill::factory()->create();

        $response = $this->get(route('billing.weekly.index'));

        $response->assertStatus(200);
        $response->assertViewHas('bills');
        $response->assertSee($bill->invoice_number);
    }

    public function test_bulk_displays_bulk_form()
    {
        $response = $this->get(route('billing.weekly.bulk'));

        $response->assertStatus(200);
        $response->assertViewHas('customers');
    }

    public function test_store_creates_weekly_bill()
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
            'period_start' => now()->startOfWeek()->toDateString(),
            'period_end' => now()->endOfWeek()->toDateString(),
            'status' => 'Generated',
            'payment_mode' => 'Cash',
            'items' => [
                [
                    'name' => $item->name,
                    'qty' => 50,
                    'rate' => 100,
                ],
            ],
        ];

        $response = $this->post(route('billing.weekly.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('weekly_bills', [
            'customer_id' => $customer->id,
            'status' => 'Generated',
            'amount' => 5000,
            'gst_percentage' => 18,
        ]);

        $this->assertDatabaseHas('weekly_bill_items', [
            'item_name' => $item->name,
            'quantity_kg' => 50,
            'rate_per_kg' => 100,
            'total_amount' => 5900, // 5000 + 18%
        ]);
    }

    public function test_bulk_store_creates_multiple_bills()
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        $payload = [
            'customer_ids' => [$customer1->id, $customer2->id],
            'period_start' => now()->startOfWeek()->toDateString(),
            'period_end' => now()->endOfWeek()->toDateString(),
            'amount' => 10000,
            'status' => 'Generated',
            'payment_mode' => 'Cash',
        ];

        $response = $this->post(route('billing.weekly.bulkStore'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('weekly_bills', [
            'customer_id' => $customer1->id,
            'amount' => 10000,
            'status' => 'Generated',
        ]);

        $this->assertDatabaseHas('weekly_bills', [
            'customer_id' => $customer2->id,
            'amount' => 10000,
            'status' => 'Generated',
        ]);
    }

    public function test_show_displays_invoice()
    {
        $bill = WeeklyBill::factory()->create();

        $response = $this->get(route('billing.weekly.show', $bill));

        $response->assertStatus(200);
        $response->assertViewHas('bill');
    }


    public function test_whatsapp_redirects()
    {
        $customer = Customer::factory()->create(['phone' => '1234567890']);
        $bill = WeeklyBill::factory()->create(['customer_id' => $customer->id]);

        $response = $this->get(route('billing.weekly.whatsapp', $bill));

        $response->assertRedirect();
        $this->assertStringContainsString('wa.me/911234567890', $response->headers->get('Location'));
    }

    public function test_export_downloads_csv()
    {
        WeeklyBill::factory()->count(2)->create();

        $response = $this->get(route('billing.weekly.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=weekly-billing.csv');
    }

    public function test_pdf_downloads_invoice()
    {
        $bill = WeeklyBill::factory()->create();

        $response = $this->get(route('billing.weekly.pdf', $bill));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
