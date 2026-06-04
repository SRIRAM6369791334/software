<?php

namespace Tests\Feature\Expenses;

use App\Models\Expense;
use App\Models\Emi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_expense_index_loads_and_filters_totals()
    {
        Expense::factory()->create(['amount' => 500, 'date' => now()->subMonth()]); // Last month, shouldn't be in total_expenses
        Expense::factory()->create(['amount' => 1000, 'date' => now()]); // This month

        $response = $this->get(route('expenses.index'));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.index');
        
        $response->assertViewHas('totals');
        $totals = $response->viewData('totals');
        $this->assertEquals(1000, $totals['total_expenses']);
    }

    public function test_expense_create_loads()
    {
        $response = $this->get(route('expenses.create'));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.create');
    }

    public function test_expense_store_creates_record()
    {
        $response = $this->post(route('expenses.store'), [
            'category' => 'Fuel',
            'description' => 'Petrol for delivery truck',
            'amount' => 150.50,
            'date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'category' => 'Fuel',
            'amount' => 150.50,
        ]);
    }

    public function test_expense_requires_valid_data()
    {
        $response = $this->post(route('expenses.store'), []);

        $response->assertSessionHasErrors(['category', 'description', 'amount', 'date']);
    }

    public function test_expense_destroy_deletes_record()
    {
        $expense = Expense::factory()->create(['category' => 'Fuel']);

        $response = $this->delete(route('expenses.destroy', $expense));

        $response->assertRedirect();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_expense_export()
    {
        Expense::factory()->create(['category' => 'Fuel']);

        $response = $this->get(route('expenses.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=expenses.csv');
    }

    public function test_expense_categories_loads()
    {
        $response = $this->get(route('expenses.categories'));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.categories.index');
    }

    public function test_emi_index_loads()
    {
        $response = $this->get(route('expenses.emis.index'));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.emis.index');
    }

    public function test_emi_create_loads()
    {
        $response = $this->get(route('expenses.emis.create'));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.emis.create');
    }

    public function test_emi_store_creates_record()
    {
        $response = $this->post(route('expenses.emis.store'), [
            'emi_type' => 'Bank Loan',
            'bank_name' => 'HDFC',
            'amount' => 5000,
            'due_date' => today()->addDays(5)->toDateString(),
            'status' => 'Upcoming',
        ]);

        $response->assertRedirect(route('expenses.emis.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('emis', [
            'emi_type' => 'Bank Loan',
            'amount' => 5000,
        ]);
    }

    public function test_emi_destroy_deletes_record()
    {
        $emi = Emi::factory()->create();

        $response = $this->delete(route('expenses.emis.destroy', $emi));

        $response->assertRedirect();
        $this->assertDatabaseMissing('emis', ['id' => $emi->id]);
    }
}
