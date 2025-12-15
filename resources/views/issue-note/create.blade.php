@extends('layouts.app')
@section('content')
<div class="container">
    <h3>System Issue Note</h3>
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('issue-note.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-3">
                <label>Employee Name</label>
                <select name="employee_id" id="employee_id" class="form-control">
                    <option value="">-- Select Employee --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name ?? $emp->entity_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Department</label>
                <input type="text" id="department" name="department" class="form-control" readonly>
            </div>

            <div class="col-md-3">
                <label>Entity</label>
                <input type="text" id="entity" name="entity" class="form-control" readonly>
            </div>

            <div class="col-md-3">
                <label>Location</label>
                <input type="text" id="location" name="location" class="form-control" readonly>
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

        {{-- SIGNATURE PAD --}}
        {{-- USER SIGNATURE --}}
<div class="mt-4">
    <label><strong>User Signature</strong></label>

    <div style="border:1px solid #ccccccd4; width:300px; height:150px;">
        <canvas id="user-pad" width="300" height="150"></canvas>
    </div>

    <button type="button" id="user-clear" class="btn btn-secondary mt-2">Clear</button>

    <input type="hidden" name="user_signature" id="user_signature">
</div>

{{-- MANAGER SIGNATURE --}}
<div class="mt-4">
    <label><strong>IT Manager Signature</strong></label>

    <div style="border:1px solid #ccc; width:300px; height:150px;">
        <canvas id="manager-pad" width="300" height="150"></canvas>
    </div>

    <button type="button" id="manager-clear" class="btn btn-secondary mt-2">Clear</button>
    <input type="hidden" name="manager_signature" id="manager_signature">
</div>
<button class="btn btn-primary mt-3">Save Issue Note</button> </form>

{{-- Signature Pad JS --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Load employee details
    const employeeSelect = document.getElementById('employee_id');
    employeeSelect.addEventListener('change', function() {
        const employeeId = this.value;

        if (employeeId) {
            fetch(`/employee/${employeeId}/details`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('department').value = data.department || data.department_name || 'N/A';
                    document.getElementById('entity').value = data.entity_name || 'N/A';
                    document.getElementById('location').value = data.location || 'N/A';
                })
                .catch(error => {
                    console.error('Error fetching employee details:', error);
                });
        } else {
            document.getElementById('department').value = '';
            document.getElementById('entity').value = '';
            document.getElementById('location').value = '';
        }
    });

    // USER SIGNATURE PAD
    const userCanvas = document.getElementById('user-pad');
    const userPad = new SignaturePad(userCanvas);

    document.getElementById('user-clear').addEventListener('click', () => {
        userPad.clear();
    });

    // MANAGER SIGNATURE PAD
    const managerCanvas = document.getElementById('manager-pad');
    const managerPad = new SignaturePad(managerCanvas);
    document.getElementById('manager-clear').addEventListener('click', () => {
        managerPad.clear();
    });

    // Attach signatures on submit
    document.querySelector("form").addEventListener("submit", function(e) {
        if (!userPad.isEmpty()) {
            document.getElementById('user_signature').value = userPad.toDataURL("image/png");
        }
        if (!managerPad.isEmpty()) {
            document.getElementById('manager_signature').value = managerPad.toDataURL("image/png");
        }
    });

});
</script>
@endsection
