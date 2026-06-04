<?php

namespace Tests\Feature\Masters;

use App\Models\Dealer;
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
}
