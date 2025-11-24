@extends('layouts.app')

@section('content')
<div class="container">
    <h3>System Issue Note</h3>

    <form action="{{ route('issue-note.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-4">
                <label>Employee Name</label>
                <select name="employee_id" id="employee_id" class="form-control">
                    <option value="">-- Select Employee --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Department</label>
                <input type="text" id="department" class="form-control" readonly>
            </div>

            <div class="col-md-4">
                <label>Location</label>
                <input type="text" id="location" class="form-control" readonly>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <label>System Code</label>
                <input type="text" name="system_code" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Printer Code</label>
                <input type="text" name="printer_code" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Issued Date</label>
                <input type="date" name="issued_date" class="form-control">
            </div>
        </div>

        <div class="mt-3">
            <label>Installed Software</label>
            <input type="text" name="software_installed" class="form-control">
        </div>

        <div class="mt-3">
            <label>Issued Items</label><br>
            <label><input type="checkbox" name="items[]" value="CD Drive"> CD Drive</label>
            <label><input type="checkbox" name="items[]" value="DVD RW"> DVD RW</label>
            <label><input type="checkbox" name="items[]" value="Keyboard/Mouse"> Keyboard / Mouse</label>
            <label><input type="checkbox" name="items[]" value="Modem/Adapters"> Modem/Adapters</label>
            <label><input type="checkbox" name="items[]" value="FDD"> FDD</label>
            <label><input type="checkbox" name="items[]" value="Printer"> Printer</label>
            <label><input type="checkbox" name="items[]" value="Power Cables"> Power Cables</label>
            <label><input type="checkbox" name="items[]" value="Scanner"> Scanner</label>
            <label><input type="checkbox" name="items[]" value="Driver Softwares"> Driver Softwares</label>
            <label><input type="checkbox" name="items[]" value="Others"> Others</label>
        </div>

        <button class="btn btn-primary mt-3">Save Issue Note</button>
    </form>
</div>

<script>
document.getElementById('employee_id').addEventListener('change', function() {
    let empId = this.value;

    if (empId) {
        fetch(`/employee/details/${empId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('department').value = data.department;
                document.getElementById('location').value = data.location;
            });
    }
});
</script>

@endsection
