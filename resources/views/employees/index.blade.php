@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Employee Master Page</h1>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Employee Form --}}
    <form action="{{ route('employees.store') }}" method="POST" class="mb-4">
        @csrf

        <div class="mb-3">
            <label class="form-label">Employee ID*</label>
            <input type="text" name="employee_id" value="{{ old('employee_id') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Entity Name*</label>
            <input type="text" name="entity_name" value="{{ old('entity_name') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Department Name*</label>
            <input type="text" name="department_name" value="{{ old('department_name') }}" class="form-control" required>
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-success btn-sm" style="padding: 4px 10px; font-size: 12px;">Save</button>
        </div>
    </form>

    {{-- Employee Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Employees</span>
            <input type="text" id="searchEmployee" class="form-control w-50" placeholder="Search by name...">
        </div>

        <div class="card-body p-0">
            <table class="table mb-0" id="employeeTable">
                <thead class="table-light">
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
                            <td>{{ $emp->name }}</td>
                            <td>{{ $emp->email }}</td>
                            <td>{{ $emp->phone }}</td>
                            <td>{{ $emp->entity_name }}</td>
                            <td>{{ $emp->department_name }}</td>
                            <td>{{ $emp->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchEmployee').addEventListener('keyup', function() {
    let query = this.value.trim();

    // Reset if box is cleared
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
                            <td>${emp.name}</td>
                            <td>${emp.email ?? ''}</td>
                            <td>${emp.phone ?? ''}</td>
                            <td>${emp.entity_name ?? ''}</td>
                            <td>${emp.department_name ?? ''}</td>
                            <td>${new Date(emp.created_at).toISOString().split('T')[0]}</td>
                            <td>
                                <a href="/employees/${emp.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                                <form action="/employees/${emp.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center">No matching employees found.</td></tr>`;
            }
        })
        .catch(error => console.error('Search error:', error));
});
</script>
@endpush
