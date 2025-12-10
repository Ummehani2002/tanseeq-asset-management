@extends('layouts.app')

@section('content')
<div class="container">
    <h2>SIM Transaction</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('simcards.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="transaction_type" class="form-label">Transaction Type</label>
            <select name="transaction_type" id="transaction_type" class="form-control">
                <option value="assign">Assign</option>
                <option value="return">Return</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="simcard_number" class="form-label">SIM Number</label>
            <input type="text" name="simcard_number" id="simcard_number" class="form-control" required>
        </div>

        <div class="mb-3" id="projectDiv">
            <label for="project_id" class="form-label">Project</label>
            <select name="project_id" id="project_id" class="form-control">
                <option value="">Select Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">
                        {{ $project->project_id }} - {{ $project->project_name }} ({{ $project->entity }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3" id="issueDateDiv">
            <label for="issue_date" class="form-label">Issue Date</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control">
        </div>

        <div class="mb-3" id="returnDateDiv" style="display:none;">
            <label for="return_date" class="form-label">Return Date</label>
            <input type="date" name="return_date" id="return_date" class="form-control">
        </div>

        <div class="mb-3">
            <label for="mrc" class="form-label">MRC</label>
            <input type="number" name="mrc" id="mrc" class="form-control">
        </div>

        <div class="mb-3">
            <label for="pm_dc" class="form-label">PM/DC</label>
            <input type="text" name="pm_dc" id="pm_dc" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
    const transactionType = document.getElementById('transaction_type');
    const issueDiv = document.getElementById('issueDateDiv');
    const returnDiv = document.getElementById('returnDateDiv');
    const projectDiv = document.getElementById('projectDiv');

    transactionType.addEventListener('change', function() {
        if(this.value === 'assign') {
            issueDiv.style.display = 'block';
            returnDiv.style.display = 'none';
            projectDiv.style.display = 'block';
        } else {
            issueDiv.style.display = 'none';
            returnDiv.style.display = 'block';
            projectDiv.style.display = 'none';
        }
    });
</script>
@endsection
