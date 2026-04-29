<?php

namespace Tests\Feature\Expenses;

use App\Models\Emi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmiAlertTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_emi_alerts_page_loads()
    {
        $response = $this->get('/expenses/emis/alerts');

        $response->assertStatus(200);
        $response->assertViewIs('expenses.emis.alerts');
    }

    public function test_emi_alerts_shows_upcoming_emis()
    {
        Emi::factory()->create(['loan_name' => 'Truck Loan', 'status' => 'Upcoming', 'due_date' => today()->addDay()->toDateString()]);

        $response = $this->get('/expenses/emis/alerts');

        $response->assertSee('Truck Loan');
    }

    public function test_emi_alerts_status_filter_is_upcoming_not_pending()
    {
        Emi::factory()->create(['loan_name' => 'Hidden EMI', 'status' => 'Pending']);
        Emi::factory()->create(['loan_name' => 'Visible EMI', 'status' => 'Upcoming', 'due_date' => today()->addDay()->toDateString()]);

        $response = $this->get('/expenses/emis/alerts');

        $response->assertSee('Visible EMI');
        $response->assertDontSee('Hidden EMI');
    }

    public function test_overdue_emis_are_shown_separately()
    {
        Emi::factory()->create(['loan_name' => 'Old Loan', 'status' => 'Upcoming', 'due_date' => today()->subDays(5)->toDateString()]);

        $response = $this->get('/expenses/emis/alerts');

        $response->assertViewHas('overdue');
        $response->assertSee('Old Loan');
    }

    public function test_emi_alerts_shows_emis_due_in_next_7_days_only()
    {
        Emi::factory()->create(['loan_name' => 'Close Loan', 'due_date' => today()->addDays(3)->toDateString()]);
        Emi::factory()->create(['loan_name' => 'Far Loan', 'due_date' => today()->addDays(30)->toDateString()]);

        $response = $this->get('/expenses/emis/alerts');

        $response->assertSee('Close Loan');
        $response->assertDontSee('Far Loan');
    }
}
