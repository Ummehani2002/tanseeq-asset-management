<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntityBudget;
use App\Models\Employee;

class EntityBudgetController extends Controller
{
    public function create()
    {
        $entities = Employee::all();
        $costHeads = ['Overhead', 'AMC', 'Software'];
        $expenseTypes = ['Maintenance', 'Capex Software', 'Subscription'];
        $budgets = EntityBudget::with('employee')->get();
      return view('entity_budget.create', compact('entities', 'costHeads', 'expenseTypes', 'budgets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:employees,id',
            'cost_head' => 'required|string',
            'expense_type' => 'required|string',
            'budget_2025' => 'required|numeric|min:0'
        ]);
                
        EntityBudget::create([
            'employee_id' => $validated['entity_id'],
            'cost_head' => $validated['cost_head'],
            'expense_type' => $validated['expense_type'],
            'budget_2025' => $validated['budget_2025']
        ]);

        return redirect()->back()->with('success', 'Budget created successfully');
    }
}