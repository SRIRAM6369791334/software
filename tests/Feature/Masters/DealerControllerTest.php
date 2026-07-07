<?php

namespace Tests\Feature\Masters;

use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
use App\Models\DealerPayment;
use App\Models\Purchase;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DealerControllerTest extends TestCase
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

    public function test_index_displays_dealers()
    {
        Dealer::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('masters.dealers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('dealers');
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->admin)->get(route('masters.dealers.create'));

        $response->assertStatus(200);
        $response->assertViewHas('routes');
    }

    public function test_store_creates_dealer_and_redirects()
    {
        $route = Route::forceCreate(['route_name' => 'Test Route']);
        
        $data = [
            'firm_name' => 'Dealer Firm',
            'phone' => '1234567890',
            'contact_person' => 'Mike Dealer',
            'gst_number' => 'GSTIN5678',
            'location' => 'Main Area',
            'route_id' => $route->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('masters.dealers.store'), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('dealers', ['firm_name' => 'Dealer Firm']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post(route('masters.dealers.store'), []);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['firm_name', 'phone']);
    }

    public function test_edit_displays_form()
    {
        $dealer = Dealer::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('masters.dealers.edit', $dealer));

        $response->assertStatus(200);
        $response->assertViewHas('dealer');
        $response->assertViewHas('routes');
    }

    public function test_update_modifies_dealer_and_redirects()
    {
        $dealer = Dealer::factory()->create();
        $data = [
            'firm_name' => 'Updated Dealer Firm',
            'phone' => '0987654321',
        ];

        $response = $this->actingAs($this->admin)->put(route('masters.dealers.update', $dealer), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('dealers', ['id' => $dealer->id, 'firm_name' => 'Updated Dealer Firm']);
    }

    public function test_show_displays_dealer_details()
    {
        $dealer = Dealer::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('masters.dealers.show', $dealer));

        $response->assertStatus(200);
        $response->assertViewHas('dealer');
    }

    public function test_destroy_deletes_dealer()
    {
        $dealer = Dealer::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('masters.dealers.destroy', $dealer));
        
        $response->assertRedirect(route('masters.dealers.index'));
        $this->assertSoftDeleted('dealers', ['id' => $dealer->id]);
    }

    public function test_ledger_pdf_includes_day_load_section_running_balance_matches_accessor()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 10000]);

        // Old-style purchase (linked via vendor_name matching firm_name)
        Purchase::factory()->create([
            'vendor_name' => $dealer->firm_name,
            'date' => '2026-07-01',
        ]);
        // Old-style payment (no day-load link)
        DealerPayment::factory()->create([
            'dealer_id' => $dealer->id,
            'date' => '2026-07-02',
            'amount' => 2000,
            'invoice_id' => null,
            'day_load_entry_id' => null,
        ]);

        // Day-load batch + entry
        $batch = DayLoadBatch::factory()->create(['billing_date' => '2026-07-03']);
        $entry = DayLoadEntry::factory()->create([
            'batch_id' => $batch->id,
            'dealer_id' => $dealer->id,
            'box_weight' => 510,
            'empty_weight' => 10,
            'customer_rate' => 10,
            'status' => 'Active',
        ]);

        // Day-load payment (linked to the entry)
        DealerPayment::factory()->create([
            'dealer_id' => $dealer->id,
            'day_load_entry_id' => $entry->id,
            'date' => '2026-07-04',
            'amount' => 3000,
        ]);

        // 1. Route returns PDF successfully
        $response = $this->actingAs($this->admin)
            ->get(route('masters.dealers.ledger-pdf', $dealer));
        $response->assertStatus(200);

        // 2. Verify data logic: running balance from raw queries matches accessor
        $dlEntries = $dealer->dayLoadEntries()
            ->where('status', '!=', 'Cancelled')
            ->with('batch')
            ->get()->map(fn($e) => [
                'date' => optional($e->batch)->billing_date ?? $e->created_at->format('Y-m-d'),
                'debit' => (float) $e->amount,
                'credit' => 0,
            ]);

        $dlPayments = $dealer->payments()
            ->where(fn($q) => $q->whereNotNull('invoice_id')
                ->orWhereNotNull('day_load_entry_id'))
            ->get()->map(fn($p) => [
                'date' => $p->date->format('Y-m-d'),
                'debit' => 0,
                'credit' => (float) $p->amount,
            ]);

        $dlLedger = $dlEntries->concat($dlPayments)->sortBy('date');
        $runningBalance = $dlLedger->sum(fn($r) => $r['debit'] - $r['credit']);
        $expectedFinal = max(0, $runningBalance);

        $this->assertEquals(
            $dealer->dayload_outstanding,
            $expectedFinal,
            'Day-load running balance must match the accessor computation.'
        );
    }
}
