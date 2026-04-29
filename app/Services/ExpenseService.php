<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Emi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseService
{
    public function paginatedExpenses(int $perPage = 15): LengthAwarePaginator
    {
        return Expense::latest('date')->paginate($perPage);
    }

    public function allEmis(): \Illuminate\Database\Eloquent\Collection
    {
        return Emi::orderBy('due_date')->get();
    }

    public function createExpense(array $data): Expense
    {
        return Expense::create($data);
    }

    public function deleteExpense(Expense $expense): void
    {
        $expense->delete();
    }

    public function totals(): array
    {
        return [
            'total_expenses' => Expense::whereMonth('date', now()->month)->sum('amount'),
            'total_emis'     => Emi::sum('amount'),
        ];
    }

    public function allExpensesForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return Expense::orderByDesc('date')->get();
    }
}
