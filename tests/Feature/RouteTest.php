<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_all_report_routes_return_200()
    {
        $routes = [
            '/reports/sales/daily',
            '/reports/sales/weekly',
            '/reports/sales/monthly',
            '/reports/purchases/daily',
            '/reports/purchases/weekly',
            '/reports/purchases/monthly',
            '/reports/purchases/vendor-analytics',
            '/reports/sales/export-pdf',
            '/reports/purchases/export-pdf',
            '/expenses/emis/alerts',
            '/payments/dealers',
            '/profit',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200, "Route $route failed");
        }
    }
}
