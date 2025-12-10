@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Internet Service</h3>

    <form action="{{ route('internet-services.store') }}" method="POST">
        @csrf

        {{-- Project --}}
        <div class="mb-3">
            <label class="form-label">Project</label>
            <select name="project_id" class="form-control" required>
                <option value="">-- select project --</option>
                @foreach ($projects as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->project_id }} - {{ $p->project_name }} ({{ $p->entity }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Service Type --}}
        <div class="mb-3">
            <label class="form-label">Service Type</label>
            <select name="service_type" class="form-control" required>
                <option value="simcard">SIM Card</option>
                <option value="fixed">Fixed Service</option>
                <option value="service">Out Sourced</option>
            </select>
        </div>

        {{-- Account Number --}}
        <div class="mb-3">
            <label class="form-label">Account Number</label>
            <input type="text" name="account_number" class="form-control">
        </div>

        {{-- Dates --}}
        <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="service_start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" name="service_end_date" class="form-control">
        </div>

        {{-- Person in Charge (Employee) --}}
        <div class="mb-3">
            <label class="form-label">Person in Charge</label>
            <select name="person_in_charge_id" class="form-control" required>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}">
                        {{ $emp->name }} ({{ $emp->phone }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Project Manager --}}
        <div class="mb-3">
            <label class="form-label">Project Manager</label>
            <input type="text" name="project_manager" class="form-control">
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="suspend">Suspend</option>
                <option value="closed">Closed</option>
            </select>
        </div>

        <button class="btn btn-primary">Save</button>

    </form>
</div>
@endsection
