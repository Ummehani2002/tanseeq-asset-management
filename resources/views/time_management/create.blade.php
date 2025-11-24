@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add New Job Card</h2>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <form action="{{ route('time.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="employee_id" class="form-label">Employee</label>
        <select name="employee_id" id="employee_id" class="form-select" required>
            <option value="">-- Select Employee --</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->name }}</option>

            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Project Name</label>
        <input type="text" name="project_name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Job Card Date</label>
        <input type="date" name="job_card_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Standard Man Hours</label>
        <input type="number" step="0.01" name="standard_man_hours" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Save Job</button>
</form>

</div>
@endsection
