<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BirdBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BirdBatchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup role and user
        Role::create(['name' => 'admin']);
        $this->adminUser = User::factory()->create(['is_active' => true]);
        $this->adminUser->assignRole('admin');
    }

    public function test_index_displays_batches(): void
    {
        BirdBatch::create([
            'batch_name' => 'Batch A',
            'date_received' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'avg_weight' => 0.05
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('stock.batches.index'));

        $response->assertStatus(200);
        $response->assertViewIs('stock.batches.index');
        $response->assertViewHas('batches');
        $this->assertCount(1, $response->viewData('batches'));
    }

    public function test_store_creates_new_batch(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('stock.batches.store'), [
            'batch_name' => 'Batch B',
            'date_received' => now()->toDateString(),
            'initial_count' => 500,
            'avg_weight' => 0.04
        ]);

        $response->assertRedirect(route('stock.batches.index'));
        $response->assertSessionHas('success', 'Batch created successfully.');

        $this->assertDatabaseHas('bird_batches', [
            'batch_name' => 'Batch B',
            'initial_count' => 500,
            'current_count' => 500, // Should equal initial_count
            'avg_weight' => 0.04
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('stock.batches.store'), []);

        $response->assertSessionHasErrors(['batch_name', 'date_received', 'initial_count', 'avg_weight']);
    }

    public function test_record_mortality_reduces_current_count(): void
    {
        $batch = BirdBatch::create([
            'batch_name' => 'Batch C',
            'date_received' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'avg_weight' => 0.05
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('stock.batches.mortality', $batch), [
            'count' => 10,
            'reason' => 'Disease',
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Mortality recorded successfully.');

        $this->assertDatabaseHas('bird_batches', [
            'id' => $batch->id,
            'current_count' => 990,
        ]);

        // Stock transaction should also be recorded
        $this->assertDatabaseHas('stock_transactions', [
            'txn_type' => 'OUT',
            'quantity' => 10,
            'reference_type' => BirdBatch::class,
            'reference_id' => $batch->id,
        ]);
    }
    
    public function test_record_mortality_validates_count(): void
    {
        $batch = BirdBatch::create([
            'batch_name' => 'Batch D',
            'date_received' => now()->toDateString(),
            'initial_count' => 100,
            'current_count' => 100,
            'avg_weight' => 0.05
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('stock.batches.mortality', $batch), [
            'count' => 105, // More than current count
            'reason' => 'Disease',
            'date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['count']);
    }
}
