@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Internet Service</h3>

    <form action="{{ route('internet-services.update', $internetService->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Project --}}
        <div class="mb-3">
            <label class="form-label">Project</label>
            <select name="project_id" class="form-control" required>
                @foreach ($projects as $p)
                    <option value="{{ $p->id }}" 
                        {{ $internetService->project_id == $p->id ? 'selected' : '' }}>
                        {{ $p->project_id }} - {{ $p->project_name }} ({{ $p->entity }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Service Type --}}
        <div class="mb-3">
            <label class="form-label">Service Type</label>
            <select name="service_type" class="form-control">
                <option value="simcard" {{ $internetService->service_type == 'simcard' ? 'selected' : '' }}>SIM Card</option>
                <option value="fixed"   {{ $internetService->service_type == 'fixed' ? 'selected' : '' }}>Fixed Internet</option>
                <option value="service" {{ $internetService->service_type == 'service' ? 'selected' : '' }}>Other Service</option>
            </select>
        </div>

        {{-- Account Number --}}
        <div class="mb-3">
            <label class="form-label">Account Number</label>
            <input type="text" name="account_number" class="form-control"
                   value="{{ $internetService->account_number }}">
        </div>

        {{-- Dates --}}
        <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="service_start_date" class="form-control"
                   value="{{ $internetService->service_start_date }}">
        </div>

        <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" name="service_end_date" class="form-control"
                   value="{{ $internetService->service_end_date }}">
        </div>

        {{-- Person in Charge --}}
        <div class="mb-3">
            <label class="form-label">Person in Charge</label>
            <select name="person_in_charge_id" class="form-control">
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" 
                        {{ $internetService->person_in_charge == $emp->name ? 'selected' : '' }}>
                        {{ $emp->name }} ({{ $emp->phone }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Project Manager --}}
        <div class="mb-3">
            <label class="form-label">Project Manager</label>
            <input type="text" name="project_manager" class="form-control"
                   value="{{ $internetService->project_manager }}">
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active"  {{ $internetService->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspend" {{ $internetService->status == 'suspend' ? 'selected' : '' }}>Suspend</option>
                <option value="closed"  {{ $internetService->status == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>

        <button class="btn btn-primary">Update</button>

    </form>
</div>
@endsection
