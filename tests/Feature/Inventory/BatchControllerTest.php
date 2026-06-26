<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = $this->createAdmin();
    }

    public function test_index_displays_batches(): void
    {
        Batch::create([
            'batch_code' => 'BATCH-001',
            'placement_date' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('inventory.batches.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventory.batches.index');
        $response->assertViewHas('batches');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('inventory.batches.create'));
        $response->assertStatus(200);
        $response->assertViewIs('inventory.batches.create');
    }

    public function test_store_creates_new_batch(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('inventory.batches.store'), [
            'batch_code' => 'BATCH-002',
            'placement_date' => now()->toDateString(),
            'initial_count' => 500,
            'breed' => 'Cobb 500',
            'avg_placement_weight' => 0.04
        ]);

        $response->assertRedirect(route('inventory.batches.index'));
        $response->assertSessionHas('success', 'New batch registered successfully.');

        $this->assertDatabaseHas('batches', [
            'batch_code' => 'BATCH-002',
            'initial_count' => 500,
            'current_count' => 500,
            'breed' => 'Cobb 500'
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('inventory.batches.store'), []);

        $response->assertSessionHasErrors(['batch_code', 'placement_date', 'initial_count']);
    }

    public function test_show_displays_batch(): void
    {
        $batch = Batch::create([
            'batch_code' => 'BATCH-003',
            'placement_date' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('inventory.batches.show', $batch));
        $response->assertStatus(200);
        $response->assertViewIs('inventory.batches.show');
        $response->assertViewHas('batch');
    }

    public function test_edit_displays_form(): void
    {
        $batch = Batch::create([
            'batch_code' => 'BATCH-004',
            'placement_date' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('inventory.batches.edit', $batch));
        $response->assertStatus(200);
        $response->assertViewIs('inventory.batches.edit');
        $response->assertViewHas('batch');
    }

    public function test_update_modifies_batch(): void
    {
        $batch = Batch::create([
            'batch_code' => 'BATCH-005',
            'placement_date' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('inventory.batches.update', $batch), [
            'batch_code' => 'BATCH-005-UPDATED',
            'placement_date' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 950,
            'status' => 'Active'
        ]);

        $response->assertRedirect(route('inventory.batches.index'));
        $response->assertSessionHas('success', 'Batch updated successfully.');

        $this->assertDatabaseHas('batches', [
            'id' => $batch->id,
            'batch_code' => 'BATCH-005-UPDATED',
            'current_count' => 950
        ]);
    }

    public function test_destroy_deletes_batch(): void
    {
        $batch = Batch::create([
            'batch_code' => 'BATCH-006',
            'placement_date' => now()->toDateString(),
            'initial_count' => 1000,
            'current_count' => 1000,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('inventory.batches.destroy', $batch));
        $response->assertRedirect(route('inventory.batches.index'));
        $response->assertSessionHas('success', 'Batch record deleted.');

        $this->assertDatabaseMissing('batches', [
            'id' => $batch->id
        ]);
    }
}
