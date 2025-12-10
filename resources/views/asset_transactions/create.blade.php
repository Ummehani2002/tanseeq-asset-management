@extends('layouts.app')

@section('content')
<div class="container">
   <h2>{{ isset($transaction) && $transaction ? 'Edit' : 'Add' }} Asset Transaction</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $isEdit = isset($transaction) && $transaction;
    @endphp

    <form method="POST"
          action="{{ $isEdit ? route('asset-transactions.update', $transaction->id) : route('asset-transactions.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- Asset Dropdown --}}
        <div class="mb-3">
            <label for="asset_id">Asset (Category - Serial Number)</label>
            <select id="asset_id" name="asset_id" class="form-control" required>
                <option value="">Select Asset</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}"
                        data-category="{{ $asset->assetCategory->category_name ?? '' }}"
                        @if(($isEdit && $transaction->asset_id == $asset->id) || old('asset_id') == $asset->id) selected @endif>
                        {{ $asset->assetCategory->category_name ?? 'N/A' }} - {{ $asset->serial_number }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Employee Dropdown (Laptop Only) --}}
        <div id="employee_section" class="mb-3" style="display:none;">
            <label for="employee_id">Employee Name</label>
            <select name="employee_id" id="employee_id" class="form-control">
                <option value="">Select Employee</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}"
                        @if(($isEdit && $transaction->employee_id == $emp->id) || old('employee_id') == $emp->id) selected @endif>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Project Name (Printer Only) --}}
        <div id="project_section" class="mb-3" style="display:none;">
            <label for="project_name">Project Name</label>
            <input type="text" name="project_name" id="project_name" class="form-control"
                   value="{{ $isEdit ? $transaction->project_name : old('project_name') }}">
        </div>

        {{-- Transaction Type --}}
        <div class="mb-3">
            <label for="transaction_type">Transaction Type</label>
            <select id="transaction_type" name="transaction_type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="assign" @if(($isEdit && $transaction->transaction_type == 'assign') || old('transaction_type') == 'assign') selected @endif>Assign</option>
                <option value="return" @if(($isEdit && $transaction->transaction_type == 'return') || old('transaction_type') == 'return') selected @endif>Return</option>
                <option value="system_maintenance" @if(($isEdit && $transaction->transaction_type == 'system_maintenance') || old('transaction_type') == 'system_maintenance') selected @endif>System Maintenance</option>
            </select>
        </div>

        {{-- Assign Fields --}}
        <div id="assign_fields" style="display:none;">
            <div class="mb-3">
                <label for="issue_date">Issue Date</label>
                <input type="date" name="issue_date" id="issue_date" class="form-control"
                       value="{{ $isEdit ? $transaction->issue_date : old('issue_date') }}">
            </div>
            <div class="mb-3">
                <label for="asset_image">Asset Image</label>
                <input type="file" name="asset_image" id="asset_image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="location">Location</label>
                <select name="location" id="location" class="form-control required-assign">
                    <option value="">Select Location</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->location_name }}">
                            {{ $loc->location_name }} ({{ $loc->location_id }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Return Fields --}}
        <div id="return_fields" style="display:none;">
            <div class="mb-3">
                <label for="return_date">Return Date</label>
                <input type="date" name="return_date" id="return_date" class="form-control"
                       value="{{ $isEdit ? $transaction->return_date : old('return_date') }}">
            </div>
            <div class="mb-3">
                <label for="asset_image_return">Asset Image</label>
                <input type="file" name="asset_image_return" id="asset_image_return" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="location_return">Location</label>
                <select name="location_return" id="location_return" class="form-control required-return">
                    <option value="">Select Location</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->location_name }}">
                            {{ $loc->location_name }} ({{ $loc->location_id }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- System Maintenance Fields --}}
        <div id="system_maintenance_fields" style="display:none;">
            <div class="mb-3">
                <label for="assigned_to">Assigned To</label>
                <input type="text" name="assigned_to" id="assigned_to" class="form-control"
                       value="{{ $isEdit ? $transaction->assigned_to : old('assigned_to') }}">
            </div>
            <div class="mb-3">
                <label for="receive_date">Receive Date</label>
                <input type="date" name="receive_date" id="receive_date" class="form-control"
                       value="{{ $isEdit ? $transaction->receive_date : old('receive_date') }}">
            </div>
            <div class="mb-3">
                <label for="delivery_date">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                       value="{{ $isEdit ? $transaction->delivery_date : old('delivery_date') }}">
            </div>
            <div class="mb-3">
                <label for="repair_type">Repair Type</label>
                <select name="repair_type" id="repair_type" class="form-control">
                    <option value="">-- Select Repair Type --</option>
                    <option value="Hardware Replacement">Hardware Replacement</option>
                    <option value="Software Installation">Software Installation</option>
                    <option value="Preventive Maintenance">Preventive Maintenance</option>
                    <option value="On Call Service">On Call Service</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="maintenance_summary">Maintenance Summary</label>
                <textarea name="maintenance_summary" id="maintenance_summary" class="form-control"></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
            {{ $isEdit ? 'Update' : 'Save' }} Transaction
        </button>
    </form>
</div>

<script>
// Add/remove required from hidden fields
function toggleRequired(section, enable) {
    const inputs = section.querySelectorAll('[name]');
    inputs.forEach(input => {
        if (enable) {
            input.setAttribute('required', 'required');
        } else {
            input.removeAttribute('required');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {

    const assetDropdown = document.getElementById('asset_id');
    const employeeSection = document.getElementById('employee_section');
    const projectSection = document.getElementById('project_section');

    const transactionType = document.getElementById('transaction_type');
    const assignFields = document.getElementById('assign_fields');
    const returnFields = document.getElementById('return_fields');
    const systemMaintenanceFields = document.getElementById('system_maintenance_fields');

    function handleAssetChange() {
        const category = assetDropdown.options[assetDropdown.selectedIndex]?.dataset.category?.toLowerCase() || '';

        employeeSection.style.display = category === 'laptop' ? 'block' : 'none';
        projectSection.style.display = category === 'printer' ? 'block' : 'none';
    }

    function handleTransactionTypeChange() {
        assignFields.style.display = 'none';
        returnFields.style.display = 'none';
        systemMaintenanceFields.style.display = 'none';

        toggleRequired(assignFields, false);
        toggleRequired(returnFields, false);
        toggleRequired(systemMaintenanceFields, false);

        if (transactionType.value === 'assign') {
            assignFields.style.display = 'block';
            toggleRequired(assignFields, true);
        }
        if (transactionType.value === 'return') {
            returnFields.style.display = 'block';
            toggleRequired(returnFields, true);
        }
        if (transactionType.value === 'system_maintenance') {
            systemMaintenanceFields.style.display = 'block';
            toggleRequired(systemMaintenanceFields, true);
        }
    }

    assetDropdown.addEventListener('change', handleAssetChange);
    transactionType.addEventListener('change', handleTransactionTypeChange);

    handleAssetChange();
    handleTransactionTypeChange();
});
</script>
@endsection
