@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ isset($transaction) ? 'Edit' : 'Add' }} Asset Transaction</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php $isEdit = isset($transaction); @endphp

    <form method="POST" 
          action="{{ $isEdit ? route('asset-transactions.update', $transaction->id) : route('asset-transactions.store') }}" 
          enctype="multipart/form-data" id="transactionForm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Asset Category --}}
        <div class="mb-3">
            <label for="asset_category_id">Asset Category <span class="text-danger">*</span></label>
            <select name="asset_category_id" id="asset_category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" 
                        @if(old('asset_category_id', $transaction->asset->asset_category_id ?? '') == $cat->id) selected @endif>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Asset Selection (with Serial Number) --}}
        <div class="mb-3" id="asset_selection_section" style="display:none;">
            <label for="asset_id">Asset (Serial Number) <span class="text-danger">*</span></label>
            <select name="asset_id" id="asset_id" class="form-control" required>
                <option value="">Select Category First</option>
                @if($isEdit && $transaction->asset)
                    <option value="{{ $transaction->asset->id }}" selected>
                        {{ $transaction->asset->assetCategory->category_name ?? 'N/A' }} - {{ $transaction->asset->serial_number }}
                    </option>
                @endif
            </select>
            <small class="text-muted" id="asset_status_info"></small>
        </div>

        {{-- Employee/Project Selection (based on category) --}}
        {{-- Employee Dropdown (for Laptop) --}}
        <div class="mb-3" id="employee_section" style="display:none;">
            <label for="employee_id">Employee Name <span class="text-danger" id="employee_required">*</span></label>
            <select name="employee_id" id="employee_id" class="form-control">
                <option value="">Select Employee</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}"
                        @if(old('employee_id', $transaction->employee_id ?? '') == $emp->id) selected @endif>
                        {{ $emp->name }} ({{ $emp->email }})
                    </option>
                @endforeach
            </select>
            <small class="text-muted" id="employee_auto_fill_info"></small>
        </div>

        {{-- Project Name (for Printer) --}}
        <div class="mb-3" id="project_section" style="display:none;">
            <label for="project_name">Project Name <span class="text-danger" id="project_required">*</span></label>
            <input type="text" name="project_name" id="project_name" class="form-control"
                   value="{{ old('project_name', $transaction->project_name ?? '') }}"
                   placeholder="Enter project name">
            <small class="text-muted" id="project_auto_fill_info"></small>
            <select id="project_select" class="form-control mt-2" onchange="document.getElementById('project_name').value = this.value">
                <option value="">Or select existing project</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->project_name }}">{{ $proj->project_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Location (for Laptop only) --}}
        <div class="mb-3" id="location_section" style="display:none;">
            <label for="location_id">Location</label>
            <select name="location_id" id="location_id" class="form-control">
                <option value="">Select Location</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" 
                        @if(old('location_id', $transaction->location_id ?? '') == $loc->id) selected @endif>
                        {{ $loc->location_name }} ({{ $loc->location_id }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Transaction Type --}}
        <div class="mb-3" id="transaction_type_section" style="display:none;">
            <label for="transaction_type">Transaction Type <span class="text-danger">*</span></label>
            <select name="transaction_type" id="transaction_type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="assign" @if(old('transaction_type', $transaction->transaction_type ?? '') == 'assign') selected @endif>Assign</option>
                <option value="return" @if(old('transaction_type', $transaction->transaction_type ?? '') == 'return') selected @endif>Return</option>
                <option value="system_maintenance" @if(old('transaction_type', $transaction->transaction_type ?? '') == 'system_maintenance') selected @endif>System Maintenance</option>
            </select>
            <small class="text-muted" id="transaction_type_info"></small>
        </div>

        {{-- Transaction Specific Fields --}}
        {{-- Assign Fields --}}
        <div class="mb-3" id="assign_fields" style="display:none;">
            <label for="issue_date">Issue Date</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control"
                   value="{{ old('issue_date', $transaction->issue_date ?? date('Y-m-d')) }}">
        </div>

        {{-- Return Fields --}}
        <div class="mb-3" id="return_fields" style="display:none;">
            <label for="return_date">Return Date</label>
            <input type="date" name="return_date" id="return_date" class="form-control"
                   value="{{ old('return_date', $transaction->return_date ?? date('Y-m-d')) }}">
        </div>

        {{-- Maintenance Fields --}}
        <div id="maintenance_fields" style="display:none;">
            <div class="mb-3">
                <label for="receive_date">Receive Date (When asset received for maintenance)</label>
                <input type="date" name="receive_date" id="receive_date" class="form-control"
                       value="{{ old('receive_date', $transaction->receive_date ?? date('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label for="delivery_date">Delivery Date (When asset returned from maintenance)</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                       value="{{ old('delivery_date', $transaction->delivery_date ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="repair_type">Repair Type</label>
                <select name="repair_type" id="repair_type" class="form-control">
                    <option value="">Select Repair Type</option>
                    <option value="Hardware Replacement" @if(old('repair_type', $transaction->repair_type ?? '') == 'Hardware Replacement') selected @endif>Hardware Replacement</option>
                    <option value="Software Installation" @if(old('repair_type', $transaction->repair_type ?? '') == 'Software Installation') selected @endif>Software Installation</option>
                    <option value="Preventive Maintenance" @if(old('repair_type', $transaction->repair_type ?? '') == 'Preventive Maintenance') selected @endif>Preventive Maintenance</option>
                    <option value="On Call Service" @if(old('repair_type', $transaction->repair_type ?? '') == 'On Call Service') selected @endif>On Call Service</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="bi bi-check-circle me-2"></i>{{ $isEdit ? 'Update' : 'Save' }} Transaction
        </button>
        <a href="{{ route('asset-transactions.index') }}" class="btn btn-secondary">
            <i class="bi bi-x-circle me-2"></i>Cancel
        </a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryDropdown = document.getElementById('asset_category_id');
    const assetDropdown = document.getElementById('asset_id');
    const employeeSection = document.getElementById('employee_section');
    const projectSection = document.getElementById('project_section');
    const locationSection = document.getElementById('location_section');
    const transactionType = document.getElementById('transaction_type');
    const transactionTypeSection = document.getElementById('transaction_type_section');
    const assignFields = document.getElementById('assign_fields');
    const returnFields = document.getElementById('return_fields');
    const maintenanceFields = document.getElementById('maintenance_fields');
    const assetStatusInfo = document.getElementById('asset_status_info');
    const employeeAutoFillInfo = document.getElementById('employee_auto_fill_info');
    const projectAutoFillInfo = document.getElementById('project_auto_fill_info');
    const transactionTypeInfo = document.getElementById('transaction_type_info');
    const assetSelectionSection = document.getElementById('asset_selection_section');

    let currentCategory = '';
    let assetDetails = null;

    // Handle category change
    categoryDropdown.addEventListener('change', function() {
        const categoryId = this.value;
        currentCategory = '';
        
        if (!categoryId) {
            assetSelectionSection.style.display = 'none';
            hideAllFields();
            return;
        }

        // Show asset selection
        assetSelectionSection.style.display = 'block';
        assetDropdown.innerHTML = '<option value="">Loading assets...</option>';

        // Fetch assets for this category
        fetch(`/asset-transactions/get-assets-by-category/${categoryId}`)
            .then(res => res.json())
            .then(assets => {
                assetDropdown.innerHTML = '<option value="">Select Asset</option>';
                assets.forEach(asset => {
                    const option = document.createElement('option');
                    option.value = asset.id;
                    option.textContent = `${asset.serial_number} (${asset.asset_id}) - Status: ${asset.status}`;
                    option.dataset.status = asset.status;
                    option.dataset.category = asset.category_name.toLowerCase();
                    assetDropdown.appendChild(option);
                });
                
                if (assets.length > 0) {
                    currentCategory = assets[0].category_name.toLowerCase();
                }
            })
            .catch(err => {
                console.error('Error loading assets:', err);
                assetDropdown.innerHTML = '<option value="">Error loading assets</option>';
            });
    });

    // Handle asset selection
    assetDropdown.addEventListener('change', function() {
        const assetId = this.value;
        
        if (!assetId) {
            hideAssignmentFields();
            return;
        }

        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.status) {
            const status = selectedOption.dataset.status;
            const category = selectedOption.dataset.category || '';
            currentCategory = category;
            
            assetStatusInfo.textContent = `Current Status: ${status}`;
            assetStatusInfo.className = 'text-muted';

            // Fetch asset details including previous transaction info
            fetch(`/asset-transactions/get-asset-details/${assetId}`)
                .then(res => res.json())
                .then(data => {
                    assetDetails = data;
                    updateTransactionTypeOptions(data);
                    showAssignmentFields(data, category);
                })
                .catch(err => {
                    console.error('Error loading asset details:', err);
                });
        }
    });

    // Handle transaction type change
    transactionType.addEventListener('change', function() {
        const txType = this.value;
        
        assignFields.style.display = 'none';
        returnFields.style.display = 'none';
        maintenanceFields.style.display = 'none';

        if (!txType) return;

        if (txType === 'assign') {
            assignFields.style.display = 'block';
        } else if (txType === 'return') {
            returnFields.style.display = 'block';
        } else if (txType === 'system_maintenance') {
            maintenanceFields.style.display = 'block';
        }
    });

    function showAssignmentFields(data, category) {
        transactionTypeSection.style.display = 'block';

        // Show appropriate fields based on category
        if (category === 'laptop') {
            employeeSection.style.display = 'block';
            locationSection.style.display = 'block';
            projectSection.style.display = 'none';
            
            // Auto-fill employee if available
            if (data.current_employee_id) {
                document.getElementById('employee_id').value = data.current_employee_id;
                employeeAutoFillInfo.textContent = `Auto-filled: ${data.current_employee_name || 'Previous employee'}`;
                employeeAutoFillInfo.className = 'text-success';
            } else {
                employeeAutoFillInfo.textContent = '';
            }
            
            // Auto-fill location if available
            if (data.current_location_id) {
                document.getElementById('location_id').value = data.current_location_id;
            }
        } else if (category === 'printer') {
            projectSection.style.display = 'block';
            employeeSection.style.display = 'none';
            locationSection.style.display = 'none';
            
            // Auto-fill project if available
            if (data.current_project_name) {
                document.getElementById('project_name').value = data.current_project_name;
                projectAutoFillInfo.textContent = `Auto-filled: ${data.current_project_name}`;
                projectAutoFillInfo.className = 'text-success';
            } else {
                projectAutoFillInfo.textContent = '';
            }
        } else {
            employeeSection.style.display = 'none';
            projectSection.style.display = 'none';
            locationSection.style.display = 'none';
        }
    }

    function updateTransactionTypeOptions(data) {
        const txTypeSelect = transactionType;
        const availableTypes = data.available_transactions || [];
        
        // Clear existing options except the first one
        txTypeSelect.innerHTML = '<option value="">Select Type</option>';
        
        // Add available transaction types
        const allTypes = [
            { value: 'assign', label: 'Assign' },
            { value: 'return', label: 'Return' },
            { value: 'system_maintenance', label: 'System Maintenance' }
        ];
        
        allTypes.forEach(type => {
            if (availableTypes.includes(type.value)) {
                const option = document.createElement('option');
                option.value = type.value;
                option.textContent = type.label;
                txTypeSelect.appendChild(option);
            }
        });

        // Show info message
        if (data.status === 'under_maintenance') {
            transactionTypeInfo.textContent = 'Asset is under maintenance. You can return it or reassign to the same employee.';
            transactionTypeInfo.className = 'text-warning';
        } else if (data.status === 'assigned') {
            transactionTypeInfo.textContent = 'Asset is currently assigned. You can return it or send for maintenance.';
            transactionTypeInfo.className = 'text-info';
        } else {
            transactionTypeInfo.textContent = 'Asset is available for assignment.';
            transactionTypeInfo.className = 'text-success';
        }
    }

    function hideAssignmentFields() {
        employeeSection.style.display = 'none';
        projectSection.style.display = 'none';
        locationSection.style.display = 'none';
        transactionTypeSection.style.display = 'none';
        assignFields.style.display = 'none';
        returnFields.style.display = 'none';
        maintenanceFields.style.display = 'none';
    }

    function hideAllFields() {
        assetSelectionSection.style.display = 'none';
        hideAssignmentFields();
    }

    // Form submission handler
    const form = document.getElementById('transactionForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
            return true;
        });
    }

    // Initialize on page load
    if (categoryDropdown.value) {
        categoryDropdown.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
