<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Since dashboard requires login
        $this->user = User::factory()->create(['is_active' => true]);
    }

    public function test_dashboard_index_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHasAll(['stats', 'recentSales', 'upcomingEmis']);
    }

    public function test_dashboard_alerts_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.alerts'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.alerts');
        $response->assertViewHas('upcomingEmis');
    }

    public function test_guests_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
