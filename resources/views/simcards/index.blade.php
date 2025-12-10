@extends('layouts.app')

@section('content')
<div class="container">
    <h2>SIM Transactions</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>SIM Number</th>
                <th>Transaction Type</th>
                <th>Project</th>
                <th>Entity</th>
                <th>MRC</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>PM/DC</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $txn->simcard_number }}</td>
                    <td>{{ ucfirst($txn->transaction_type) }}</td>
                    <td>{{ $txn->project_name ?? '-' }}</td>
                    <td>{{ $txn->entity ?? '-' }}</td>
                    <td>{{ $txn->mrc ?? '-' }}</td>
                    <td>{{ $txn->issue_date ?? '-' }}</td>
                    <td>{{ $txn->return_date ?? '-' }}</td>
                    <td>{{ $txn->pm_dc ?? '-' }}</td>
                    <td>{{ $txn->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
     {{ $transactions->links() }}
</div>
@endsection
