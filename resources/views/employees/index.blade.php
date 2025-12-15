@extends('layouts.app')

@section('content')
<div class="container-fluid master-page">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="bi bi-person-badge me-2"></i>Employee Master</h2>
        <p>Manage employee information and details</p>
    </div>

    {{-- Success/Error Messages --}}
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

    {{-- Employee Form --}}
    <div class="master-form-card">
        <h5 class="mb-3" style="color: var(--primary); font-weight: 600;">Add New Employee</h5>
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                    <input type="text" name="employee_id" value="{{ old('employee_id') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Entity Name <span class="text-danger">*</span></label>
                    <input type="text" name="entity_name" value="{{ old('entity_name') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Department Name <span class="text-danger">*</span></label>
                    <input type="text" name="department_name" value="{{ old('department_name') }}" class="form-control" required>
                </div>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Save Employee
                </button>
            </div>
        </form>
    </div>

    {{-- Employee Table --}}
    <div class="master-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 style="color: white; margin: 0;"><i class="bi bi-people me-2"></i>All Employees</h5>
            <input type="text" id="searchEmployee" class="form-control" style="max-width: 300px; font-size: 14px;" placeholder="Search by name...">
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="employeeTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Entity</th>
                            <th>Department</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeeBody">
                        @foreach($employees as $emp)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $emp->employee_id }}</td>
                                <td>{{ $emp->name ?? 'N/A' }}</td>
                                <td>{{ $emp->email ?? 'N/A' }}</td>
                                <td>{{ $emp->phone ?? 'N/A' }}</td>
                                <td>{{ $emp->entity_name ?? 'N/A' }}</td>
                                <td>{{ $emp->department_name ?? 'N/A' }}</td>
                                <td>{{ $emp->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete this employee?')" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchEmployee').addEventListener('keyup', function() {
    let query = this.value.trim();

    if (query.length === 0) {
        window.location.reload();
        return;
    }

    fetch(`/employees/search?query=${query}`)
        .then(response => response.json())
        .then(data => {
            let tbody = document.getElementById('employeeBody');
            tbody.innerHTML = '';

            if (data.length > 0) {
                data.forEach((emp, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${emp.employee_id}</td>
                            <td>${emp.name || 'N/A'}</td>
                            <td>${emp.email || 'N/A'}</td>
                            <td>${emp.phone || 'N/A'}</td>
                            <td>${emp.entity_name || 'N/A'}</td>
                            <td>${emp.department_name || 'N/A'}</td>
                            <td>${new Date(emp.created_at).toISOString().split('T')[0]}</td>
                            <td>
                                <a href="/employee-master/${emp.id}/edit" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="/employee-master/${emp.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted py-4">No matching employees found.</td></tr>`;
            }
        })
        .catch(error => console.error('Search error:', error));
});
</script>
@endpush
