<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntityBudget;
use App\Models\BudgetExpense;
use App\Models\Employee;

class BudgetExpenseController extends Controller
{
  public function create()
{
    $entities = Employee::all();
    // Get unique cost heads from existing entity budgets
    $costHeads = EntityBudget::distinct()->pluck('cost_head')->toArray();
    $expenseTypes = ['Maintenance', 'Capex Software', 'Subscription'];
    return view('budget_expenses.create', compact('entities', 'costHeads', 'expenseTypes'));
}

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'entity_budget_id' => 'required|exists:entity_budgets,id',
                'expense_amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
                'description' => 'nullable|string'
            ]);

            // Check available balance
            $budget = EntityBudget::find($validated['entity_budget_id']);
            $totalExpenses = BudgetExpense::where('entity_budget_id', $budget->id)
                ->sum('expense_amount');
            
            if (($totalExpenses + $validated['expense_amount']) > $budget->budget_2025) {
                throw new \Exception('Insufficient budget balance');
            }

            $expense = BudgetExpense::create($validated);

            // Get updated budget details
            return $this->getBudgetDetails($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function getBudgetDetails(Request $request)
    {
        $budget = EntityBudget::with('employee')
            ->where('employee_id', $request->entity_id)
            ->where('cost_head', $request->cost_head)
            ->where('expense_type', $request->expense_type)
            ->first();

        if ($budget) {
            $expenses = BudgetExpense::where('entity_budget_id', $budget->id)
                ->orderBy('expense_date', 'desc')
                ->get();
                
            $totalExpenses = $expenses->sum('expense_amount');

            $formattedExpenses = $expenses->map(function ($expense) use ($budget) {
                $balanceAfter = $budget->budget_2025 - BudgetExpense::where('entity_budget_id', $budget->id)
                    ->where('created_at', '<=', $expense->created_at)
                    ->sum('expense_amount');

                return [
                    'expense_date' => date('Y-m-d', strtotime($expense->expense_date)),
                    'expense_amount' => number_format($expense->expense_amount, 2),
                    'description' => $expense->description ?: '-',
                    'entity_name' => $budget->employee->entity_name ?? 'N/A', // Changed from name to entity_name
                    'cost_head' => ucfirst($budget->cost_head),
                    'expense_type' => $budget->expense_type,
                    'balance_after' => number_format($balanceAfter, 2)
                ];
            });

            return response()->json([
                'success' => true,
                'entity_budget_id' => $budget->id,
                'entity_name' => $budget->employee->entity_name ?? 'N/A', // Changed from name to entity_name
                'cost_head' => ucfirst($budget->cost_head),
                'expense_type' => $budget->expense_type,
                'budget_amount' => number_format($budget->budget_2025, 2),
                'total_expenses' => number_format($totalExpenses, 2),
                'available_balance' => number_format($budget->budget_2025 - $totalExpenses, 2),
                'expenses' => $formattedExpenses
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No budget found for selected criteria'
        ]);
    }
}