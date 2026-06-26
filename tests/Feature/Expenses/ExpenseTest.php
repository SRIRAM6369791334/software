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
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
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

    public function test_expense_update_saves_changes()
    {
        $expense = Expense::factory()->create(['category' => 'Fuel', 'amount' => 150.00]);

        $response = $this->put(route('expenses.update', $expense), [
            'category' => 'Salary',
            'description' => 'Updated Description',
            'amount' => 200.00,
            'date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'category' => 'Salary',
            'amount' => 200.00,
            'description' => 'Updated Description',
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
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
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

    public function test_emi_edit_page_loads()
    {
        $emi = Emi::factory()->create();

        $response = $this->get(route('expenses.emis.edit', $emi));

        $response->assertStatus(200);
        $response->assertViewIs('expenses.emis.edit');
        $response->assertViewHas('emi');
    }

    public function test_emi_update_saves_changes()
    {
        $emi = Emi::factory()->create(['amount' => 5000, 'status' => 'Upcoming']);

        $response = $this->put(route('expenses.emis.update', $emi), [
            'amount' => 6000,
            'due_date' => today()->addDays(10)->toDateString(),
            'status' => 'Overdue',
        ]);

        $response->assertRedirect(route('expenses.emis.index'));
        $this->assertDatabaseHas('emis', [
            'id' => $emi->id,
            'amount' => 6000,
            'status' => 'Overdue',
        ]);
    }

    public function test_emi_pay_marks_status_paid()
    {
        $emi = Emi::factory()->create(['status' => 'Upcoming']);

        $response = $this->post(route('expenses.emis.pay', $emi));

        $response->assertRedirect();
        $this->assertDatabaseHas('emis', [
            'id' => $emi->id,
            'status' => 'Paid',
        ]);
    }

    public function test_emi_close_full_closes_all_grouped_emis()
    {
        $loanName = 'Group Loan X';
        $emi1 = Emi::factory()->create(['loan_name' => $loanName, 'status' => 'Upcoming']);
        $emi2 = Emi::factory()->create(['loan_name' => $loanName, 'status' => 'Overdue']);
        $emi3 = Emi::factory()->create(['loan_name' => 'Different Loan', 'status' => 'Upcoming']);

        $response = $this->post(route('expenses.emis.close-full', $emi1));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('emis', [
            'id' => $emi1->id,
            'status' => 'Paid',
        ]);
        
        $this->assertDatabaseHas('emis', [
            'id' => $emi2->id,
            'status' => 'Paid',
        ]);

        $this->assertDatabaseHas('emis', [
            'id' => $emi3->id,
            'status' => 'Upcoming',
        ]);
    }

    public function test_emi_store_creates_vendor_record()
    {
        $vendor = \App\Models\Vendor::factory()->create();

        $response = $this->post(route('expenses.emis.store'), [
            'emi_type' => 'Vendor',
            'entity_id' => $vendor->id,
            'amount' => 3000,
            'due_date' => today()->addDays(5)->toDateString(),
            'status' => 'Upcoming',
        ]);

        $response->assertRedirect(route('expenses.emis.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('emis', [
            'emi_type' => 'Vendor',
            'entity_id' => $vendor->id,
            'amount' => 3000,
        ]);
    }
}
