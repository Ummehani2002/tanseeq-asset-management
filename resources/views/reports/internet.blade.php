@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Internet Service Report</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Entity</th>
                <th>Service Type</th>
                <th>Account Number</th>
                <th>Person In Charge</th>
                <th>Contact</th>
                <th>Service Start</th>
                <th>Service End</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($internetServices as $service)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $service->project_name ?? '-' }}</td>
                    <td>{{ $service->entity ?? '-' }}</td>
                    <td>{{ ucfirst($service->service_type) }}</td>
                    <td>{{ $service->account_number ?? '-' }}</td>
                    <td>{{ $service->person_in_charge ?? '-' }}</td>
                    <td>{{ $service->contact_details ?? '-' }}</td>
                    <td>{{ $service->service_start_date ?? '-' }}</td>
                    <td>{{ $service->service_end_date ?? '-' }}</td>
                    <td>{{ ucfirst($service->status) }}</td>
                    <td>{{ $service->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $internetServices->links() }}
</div>
@endsection
