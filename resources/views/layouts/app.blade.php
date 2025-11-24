<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
        }
        .sidebar a, .sidebar button {
            color: #ccc;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
        }
        .sidebar a:hover, .sidebar button:hover {
            background-color: #457bb0ff;
            color: white;
        }
        .collapse a {
            padding-left: 40px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <h4 class="text-center mb-3">Menu</h4>

        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary mb-2">Dashboard</a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-primary mb-2">Users</a>

        <!-- Employee Master Dropdown -->
        <button class="btn btn-outline-primary mb-2" 
                data-bs-toggle="collapse" 
                data-bs-target="#employeeMenu" 
                aria-expanded="false" 
                aria-controls="employeeMenu">
            Employee Master ▾
        </button>
        <div class="collapse" id="employeeMenu">
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-light mb-1"> Add Employee</a>
            <a href="{{ url('/employee-assets') }}" class="btn btn-sm btn-outline-light mb-1"> Employee Asset Lookup</a>
        </div>

        <!-- Location Master Dropdown -->
        <button class="btn btn-outline-primary mb-2"
                data-bs-toggle="collapse"
                data-bs-target="#locationMenu"
                aria-expanded="false"
                aria-controls="locationMenu">
            Location Master ▾
        </button>
        <div class="collapse" id="locationMenu">
            <a href="{{ route('location-master.store') }}" class="btn btn-sm btn-outline-light mb-1"> Add Location</a>
            <a href="{{ url('/location-assets') }}" class="btn btn-sm btn-outline-light mb-1"> Location Asset Lookup</a>
        </div>

        <a href="{{ route('categories.manage') }}" class="btn btn-outline-primary mb-2">Brand Management</a>
        <a href="{{ route('assets.create') }}" class="btn btn-outline-primary mb-2">Asset Master</a>
        <a href="{{ route('asset-transactions.create') }}" class="btn btn-outline-primary mb-2">Asset Transaction</a>

        <a href="{{ route('time.index') }}" 
           class="nav-link {{ request()->routeIs('time.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history me-2"></i> 
            <span>Time Management</span>
        </a>

        <!-- Budget Maintenance Dropdown -->
        <button class="btn btn-outline-primary mb-2"
                data-bs-toggle="collapse"
                data-bs-target="#budgetMenu"
                aria-expanded="false"
                aria-controls="budgetMenu">
            Budget Maintenance ▾
        </button>
        <div class="collapse" id="budgetMenu">
            <a href="{{ route('entity_budget.create') }}" class="btn btn-sm btn-outline-light mb-1"> Add Entity Budget</a>
            <a href="{{ route('budget-expenses.create') }}" class="btn btn-sm btn-outline-light mb-1"> Budget Expenses</a>
        </div>
        <!-- IT Forms Dropdown -->
<button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#issueNoteMenu"
        aria-expanded="false"
        aria-controls="issueNoteMenu">
    IT Forms ▾
</button>

<div class="collapse" id="issueNoteMenu">
    <a href="{{ route('issue-note.create') }}" class="btn btn-sm btn-outline-light mb-1">
        Create Issue Note
    </a>
</div>
    </div>

    {{-- Page Content --}}
    <div class="content">
        @yield('content')
    </div>

    <!-- ✅ Bootstrap JS Bundle (needed for collapse dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
