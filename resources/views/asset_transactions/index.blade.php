@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Asset Transactions</h2>
    <a href="{{ route('asset-transactions.create') }}" class="btn btn-primary mb-3">Add Transaction</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset</th>
                <th>Type</th>
                <th>Assigned To</th>
                <th>Date</th>
                <th>Action</th>
            </tr> 
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->asset->serial_number ?? 'N/A' }}</td>
                    <td>{{ $t->transaction_type }}</td>
                    <td>{{ $t->employee->name ?? $t->assigned_to ?? 'N/A' }}</td>
                    <td>{{ $t->issue_date ?? $t->return_date ?? $t->receive_date ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('asset-transactions.edit', $t->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('asset-transactions.destroy', $t->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
