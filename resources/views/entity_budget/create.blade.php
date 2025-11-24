
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Entity Budget</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('entity_budget.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="entity_id">Entity</label>
                <select name="entity_id" id="entity_id" class="form-control" required>
                    <option value="">Select Entity</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">{{ $entity->entity_name }}</option>
                    @endforeach
                </select>
            </div>

           <div class="col-md-4 mb-3">
    <label for="cost_head">Cost Head</label>
    <input type="text" name="cost_head" id="cost_head" class="form-control" required 
           placeholder="Enter Cost Head">
</div>

            <div class="col-md-4 mb-3">
                <label for="expense_type">Expense Type</label>
                <select name="expense_type" id="expense_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Capex Software">Capex Software</option>
                    <option value="Subscription">Subscription</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="budget_2025">Budget 2025</label>
                <input type="number" step="0.01" name="budget_2025" id="budget_2025" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Budget</button>
    </form>

    <div class="mt-4">
        <h4>Existing Budgets</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Entity</th>
                    <th>Cost Head</th>
                    <th>Expense Type</th>
                    <th>Budget 2025</th>
                    <th>Available Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $budget)
                <tr>
                    <td>{{ $budget->employee->entity_name }}</td>
                    <td>{{ ucfirst($budget->cost_head) }}</td>
                    <td>{{ $budget->expense_type }}</td>
                    <td>{{ number_format($budget->budget_2025, 2) }}</td>
                    <td>{{ number_format($budget->budget_2025 - $budget->expenses->sum('expense_amount'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection