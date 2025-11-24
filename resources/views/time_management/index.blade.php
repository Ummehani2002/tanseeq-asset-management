@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Time Management Records</h2>

    <a href="{{ route('time.create') }}" class="btn btn-primary mb-3">+ Add New Job</a>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Ticket</th>
                <th>Employee</th>
                <th>Project</th>
                <th>Job Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Duration (hrs)</th>
                <th>Status</th>
                <th>Performance (%)</th>
                <th>Delayed (Days)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $key => $r)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $r->ticket_number }}</td>
                <td>{{ $r->employee_name }}</td>
                <td>{{ $r->project_name }}</td>
                <td>{{ $r->job_card_date }}</td>
                <td>{{ $r->start_time ? $r->start_time->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $r->end_time ? $r->end_time->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $r->duration_hours ?? '-' }}</td>
                <td>{{ ucfirst($r->status) }}</td>
                <td>{{ $r->performance_percent ?? '-' }}</td>
                <td>{{ $r->delayed_days ?? '-' }}</td>
                <td>
                    @if($r->status != 'completed')
                        <a href="{{ route('time.edit', $r->id) }}" class="btn btn-sm btn-success">Complete</a>
                    @endif
                    <form action="{{ route('time.destroy', $r->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
