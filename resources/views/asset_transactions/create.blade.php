@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ isset($transaction) ? 'Edit' : 'Add' }} Asset Transaction</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php $isEdit = isset($transaction); @endphp

    <form method="POST" 
          action="{{ $isEdit ? route('asset-transactions.update', $transaction->id) : route('asset-transactions.store') }}" 
          enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Asset Category Dropdown --}}
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

        {{-- Asset Dropdown --}}
        <div class="mb-3">
            <label for="asset_id">Asset <span class="text-danger">*</span></label>
            <select name="asset_id" id="asset_id" class="form-control" required>
                <option value="">Select Category First</option>
                @if($isEdit && $transaction->asset)
                    <option value="{{ $transaction->asset->id }}" selected>
                        {{ $transaction->asset->assetCategory->category_name ?? 'N/A' }} - {{ $transaction->asset->serial_number }}
                        @if($transaction->asset->status != 'available') ({{ ucfirst($transaction->asset->status) }}) @endif
                    </option>
                @endif
            </select>
            <small class="text-muted" id="asset_status_info"></small>
        </div>

        {{-- Transaction Type --}}
        <div class="mb-3">
            <label for="transaction_type">Transaction Type <span class="text-danger">*</span></label>
            <select name="transaction_type" id="transaction_type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="assign" @if(old('transaction_type', $transaction->transaction_type ?? '') == 'assign') selected @endif>Assign</option>
                <option value="return" @if(old('transaction_type', $transaction->transaction_type ?? '') == 'return') selected @endif>Return</option>
                <option value="system_maintenance" @if(old('transaction_type', $transaction->transaction_type ?? '') == 'system_maintenance') selected @endif>System Maintenance</option>
            </select>
        </div>

        {{-- Employee Dropdown (for Laptop) --}}
        <div id="employee_section" class="mb-3" style="display:none;">
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
        </div>

        {{-- Project Name (for Printer) --}}
        <div id="project_section" class="mb-3" style="display:none;">
            <label for="project_name">Project Name <span class="text-danger" id="project_required">*</span></label>
            <input type="text" name="project_name" id="project_name" class="form-control"
                   value="{{ old('project_name', $transaction->project_name ?? '') }}"
                   placeholder="Enter project name">
            <small class="text-muted">You can also select from existing projects below</small>
            <select id="project_select" class="form-control mt-2" onchange="document.getElementById('project_name').value = this.value">
                <option value="">Or select existing project</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->project_name }}">{{ $proj->project_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Assign Fields --}}
        <div id="assign_fields" style="display:none;">
            <div class="mb-3">
                <label for="issue_date">Issue Date</label>
                <input type="date" name="issue_date" id="issue_date" class="form-control"
                       value="{{ old('issue_date', $transaction->issue_date ?? '') }}">
            </div>
            <div class="mb-3">
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
        </div>

        {{-- Return Fields --}}
        <div id="return_fields" style="display:none;">
            <div class="mb-3">
                <label for="return_date">Return Date</label>
                <input type="date" name="return_date" id="return_date" class="form-control"
                       value="{{ old('return_date', $transaction->return_date ?? '') }}">
            </div>
        </div>

        {{-- Maintenance Fields --}}
        <div id="maintenance_fields" style="display:none;">
            <div class="mb-3">
                <label for="receive_date">Receive Date (When asset received for maintenance)</label>
                <input type="date" name="receive_date" id="receive_date" class="form-control"
                       value="{{ old('receive_date', $transaction->receive_date ?? '') }}">
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

        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }} Transaction</button>
        <a href="{{ route('asset-transactions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryDropdown = document.getElementById('asset_category_id');
    const assetDropdown = document.getElementById('asset_id');
    const employeeSection = document.getElementById('employee_section');
    const projectSection = document.getElementById('project_section');
    const employeeRequired = document.getElementById('employee_required');
    const projectRequired = document.getElementById('project_required');
    const transactionType = document.getElementById('transaction_type');
    const assignFields = document.getElementById('assign_fields');
    const returnFields = document.getElementById('return_fields');
    const maintenanceFields = document.getElementById('maintenance_fields');
    const assetStatusInfo = document.getElementById('asset_status_info');

    let currentCategory = '';

    // Handle category change - load assets for that category
    categoryDropdown.addEventListener('change', function() {
        const categoryId = this.value;
        currentCategory = '';
        
        if (!categoryId) {
            assetDropdown.innerHTML = '<option value="">Select Category First</option>';
            employeeSection.style.display = 'none';
            projectSection.style.display = 'none';
            return;
        }

        // Fetch assets for this category
        fetch(`/asset-transactions/get-assets-by-category/${categoryId}`)
            .then(res => res.json())
            .then(assets => {
                assetDropdown.innerHTML = '<option value="">Select Asset</option>';
                assets.forEach(asset => {
                    const option = document.createElement('option');
                    option.value = asset.id;
                    option.textContent = `${asset.category_name} - ${asset.serial_number} (${asset.asset_id})`;
                    if (asset.status !== 'available') {
                        option.textContent += ` - Status: ${asset.status}`;
                        option.disabled = transactionType.value === 'assign';
                    }
                    option.dataset.status = asset.status;
                    option.dataset.category = asset.category_name.toLowerCase();
                    assetDropdown.appendChild(option);
                });
                
                // Get category name for field visibility
                if (assets.length > 0) {
                    currentCategory = assets[0].category_name.toLowerCase();
                    updateFieldVisibility();
                }
            })
            .catch(err => {
                console.error('Error loading assets:', err);
                assetDropdown.innerHTML = '<option value="">Error loading assets</option>';
            });
    });

    // Handle asset change
    assetDropdown.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.status) {
            const status = selectedOption.dataset.status;
            const category = selectedOption.dataset.category || '';
            currentCategory = category;
            
            assetStatusInfo.textContent = `Current Status: ${status}`;
            if (status !== 'available' && transactionType.value === 'assign') {
                assetStatusInfo.textContent += ' - Asset must be available for assignment';
                assetStatusInfo.className = 'text-danger';
            } else {
                assetStatusInfo.className = 'text-muted';
            }
            
            updateFieldVisibility();
            
            // For return/maintenance, auto-fill employee if laptop
            if ((transactionType.value === 'return' || transactionType.value === 'system_maintenance') && category === 'laptop') {
                fetch(`/asset-transactions/get-latest-employee/${this.value}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.employee_id) {
                            document.getElementById('employee_id').value = data.employee_id;
                        }
                    });
            }
        }
    });

    // Handle transaction type change
    transactionType.addEventListener('change', function() {
        assignFields.style.display = 'none';
        returnFields.style.display = 'none';
        maintenanceFields.style.display = 'none';

        // Show relevant fields
        if (this.value === 'assign') {
            assignFields.style.display = 'block';
        } else if (this.value === 'return') {
            returnFields.style.display = 'block';
        } else if (this.value === 'system_maintenance') {
            maintenanceFields.style.display = 'block';
        }

        updateFieldVisibility();
        
        // Check asset availability for assign
        const selectedAsset = assetDropdown.options[assetDropdown.selectedIndex];
        if (this.value === 'assign' && selectedAsset && selectedAsset.dataset.status !== 'available') {
            assetStatusInfo.textContent = 'Asset must be available for assignment';
            assetStatusInfo.className = 'text-danger';
        } else if (selectedAsset && selectedAsset.dataset.status) {
            assetStatusInfo.textContent = `Current Status: ${selectedAsset.dataset.status}`;
            assetStatusInfo.className = 'text-muted';
        }
    });

    function updateFieldVisibility() {
        const txType = transactionType.value;
        
        // For laptops: show employee field for assign, return, and maintenance
        if (currentCategory === 'laptop') {
            if (txType === 'assign' || txType === 'return' || txType === 'system_maintenance') {
                employeeSection.style.display = 'block';
                employeeRequired.style.display = txType === 'assign' ? 'inline' : 'none';
                document.getElementById('employee_id').required = txType === 'assign';
            } else {
                employeeSection.style.display = 'none';
            }
            projectSection.style.display = 'none';
        }
        // For printers: show project field for assign
        else if (currentCategory === 'printer') {
            if (txType === 'assign') {
                projectSection.style.display = 'block';
                projectRequired.style.display = 'inline';
                document.getElementById('project_name').required = true;
            } else {
                projectSection.style.display = 'none';
            }
            employeeSection.style.display = 'none';
        }
        // For other categories
        else {
            employeeSection.style.display = 'none';
            projectSection.style.display = 'none';
        }
    }

    // Initialize on page load
    if (categoryDropdown.value) {
        categoryDropdown.dispatchEvent(new Event('change'));
    }
    if (transactionType.value) {
        transactionType.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
