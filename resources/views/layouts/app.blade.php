<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Management</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #1F2A44;       /* Navy Blue */
            --secondary: #C6A87D;     /* Beige / Gold */
            --hover: #2C3E66;
            --bg-light: #F7F8FA;
            --white: #FFFFFF;
            --text-dark: #1F2A44;
            --border-light: #E5E7EB;
        }

        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Inter', 'Roboto', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--primary);
            padding-top: 20px;
            color: #fff;
        }

        .sidebar h4 {
            color: var(--secondary);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar a,
        .sidebar button {
            color: #EAECEF;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            transition: all 0.25s ease;
        }

        .sidebar a:hover,
        .sidebar button:hover {
            background-color: var(--hover);
            color: var(--secondary);
        }

        .collapse a {
            padding-left: 40px;
            font-size: 13px;
        }

        /* Content */
        .content {
            flex-grow: 1;
            padding: 24px;
            background-color: var(--bg-light);
            min-height: 100vh;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--hover);
            border-color: var(--hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(31, 42, 68, 0.2);
        }

        .btn-success {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: var(--primary);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #B8966A;
            border-color: #B8966A;
            color: var(--white);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(198, 168, 125, 0.3);
        }

        .btn-outline-primary {
            border-color: var(--secondary);
            color: var(--secondary);
            background-color: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--secondary);
            color: var(--primary);
            border-color: var(--secondary);
            transform: translateY(-1px);
        }

        .btn-outline-light {
            border-color: rgba(255,255,255,0.4);
            color: #fff;
            background-color: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background-color: var(--secondary);
            color: var(--primary);
            border-color: var(--secondary);
            transform: translateY(-1px);
        }

        /* Tables */
        table {
            background-color: var(--white);
            border-radius: 8px;
            overflow: hidden;
        }

        table thead {
            background-color: var(--primary);
            color: #fff;
        }

        table tbody tr:nth-child(even) {
            background-color: #F1F3F5;
        }

        table tbody tr:hover {
            background-color: #EFE7D8;
        }

        /* Cards */
        .card {
            border: 1px solid var(--border-light);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            background-color: var(--white);
        }

        .card-header {
            background-color: var(--primary);
            color: var(--white);
            border-bottom: 2px solid var(--secondary);
        }

        .card-header.bg-transparent {
            background-color: transparent !important;
            color: var(--primary);
            border-bottom: 1px solid var(--border-light);
        }

        /* Forms */
        .form-control {
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(198, 168, 125, 0.25);
            outline: none;
        }

        label {
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 8px;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-success {
            background-color: #D4EDDA;
            color: #155724;
            border-left: 4px solid var(--secondary);
        }

        .alert-danger {
            background-color: #F8D7DA;
            color: #721C24;
            border-left: 4px solid #DC3545;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .badge.bg-secondary {
            background-color: var(--secondary) !important;
            color: var(--primary);
        }

        /* Headings */
        h1, h2, h3, h4, h5 {
            font-weight: 600;
            color: var(--primary);
        }

        /* Links */
        a {
            color: var(--secondary);
            transition: color 0.3s ease;
        }

        a:hover {
            color: var(--primary);
        }

        /* Bootstrap Color Overrides */
        .text-primary {
            color: var(--primary) !important;
        }

        .bg-primary {
            background-color: var(--primary) !important;
        }

        .border-primary {
            border-color: var(--primary) !important;
        }

        .text-secondary {
            color: var(--secondary) !important;
        }

        .bg-secondary {
            background-color: var(--secondary) !important;
            color: var(--primary) !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Additional Button Styles */
        .btn-info {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: var(--primary);
        }

        .btn-info:hover {
            background-color: #B8966A;
            border-color: #B8966A;
            color: var(--white);
        }

        .btn-outline-info {
            border-color: var(--secondary);
            color: var(--secondary);
        }

        .btn-outline-info:hover {
            background-color: var(--secondary);
            color: var(--primary);
        }

        /* Pagination */
        .pagination .page-link {
            color: var(--secondary);
            border-color: var(--border-light);
        }

        .pagination .page-link:hover {
            background-color: var(--secondary);
            color: var(--primary);
            border-color: var(--secondary);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }
    </style>
</head>

<body>

    {{-- Sidebar --}}
    {{-- Sidebar --}}
<div class="sidebar">
    <h4 class="text-center mb-3">Menu</h4>
   <a href="{{ route('dashboard') }}" class="btn btn-outline-primary mb-2">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
    <a href="{{ route('users.index') }}" class="btn btn-outline-primary mb-2">
    <i class="bi bi-people"></i> Users
</a>

    <!-- Employee Master -->
   <button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#employeeMenu"
        aria-expanded="false"
        aria-controls="employeeMenu">
    <i class="bi bi-person-badge"></i> Employee Master ▾
</button>

<div class="collapse" id="employeeMenu">
    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-person-plus"></i> Add Employee
    </a>

    <a href="{{ url('/employee-assets') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-search"></i> Employee Asset Lookup
    </a>
</div>


    <!-- SIM Card Master -->
   <!-- SIM Cards Dropdown -->
<button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#simcardMenu"
        aria-expanded="false"
        aria-controls="simcardMenu">
    <i class="bi bi-sim"></i> SIM Cards ▾
</button>

<div class="collapse" id="simcardMenu">
    <a href="{{ route('simcards.create') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-pencil-square"></i> SIM Transaction
    </a>

    <a href="{{ route('simcards.index') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-clock-history"></i> View History
    </a>
</div>


    <!-- Project Master -->
    <button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#projectMenu"
        aria-expanded="false"
        aria-controls="projectMenu">
    <i class="bi bi-kanban"></i> Project Master ▾
</button>

<div class="collapse" id="projectMenu">
    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-card-checklist"></i> Projects List
    </a>

    <a href="{{ route('projects.create') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-plus-square"></i> Create Project
    </a>
</div>


    <!-- Location Master -->
    <button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#locationMenu"
        aria-expanded="false"
        aria-controls="locationMenu">
    <i class="bi bi-geo-alt"></i> Location Master ▾
</button>

<div class="collapse" id="locationMenu">
    <a href="{{ route('location-master.store') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-geo"></i> Add Location
    </a>

    <a href="{{ url('/location-assets') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-search"></i> Location Asset Lookup
    </a>
</div>

<!-- Internet Services -->
<a href="{{ route('internet-services.index') }}" class="btn btn-outline-primary mb-2">
    <i class="bi bi-wifi"></i> Internet Services
</a>


    <!-- Brand / Asset / Budget / IT Forms -->
<a href="{{ route('categories.manage') }}" class="btn btn-outline-primary mb-2">
    <i class="bi bi-tags"></i> Brand Management
</a>

<a href="{{ route('assets.create') }}" class="btn btn-outline-primary mb-2">
    <i class="bi bi-pc-display"></i> Asset Master
</a>

<a href="{{ route('asset-transactions.create') }}" class="btn btn-outline-primary mb-2">
    <i class="bi bi-arrow-left-right"></i> Asset Transaction
</a>

<!-- Time Management -->
<a href="{{ route('time.index') }}"
   class="btn btn-outline-primary mb-2 {{ request()->routeIs('time.*') ? 'active' : '' }}">
    <i class="bi bi-clock-history"></i> Time Management
</a>

<!-- Budget Maintenance -->
<button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#budgetMenu"
        aria-expanded="false"
        aria-controls="budgetMenu">
    <i class="bi bi-wallet2"></i> Budget Maintenance ▾
</button>

<div class="collapse" id="budgetMenu">
    <a href="{{ route('entity_budget.create') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-plus-circle"></i> Add Entity Budget
    </a>

    <a href="{{ route('budget-expenses.create') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-cash-coin"></i> Budget Expenses
    </a>
</div>

<!-- IT Forms -->
<button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#issueNoteMenu"
        aria-expanded="false"
        aria-controls="issueNoteMenu">
    <i class="bi bi-file-earmark-text"></i> IT Forms ▾
</button>

<div class="collapse" id="issueNoteMenu">
    <a href="{{ route('issue-note.create') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-journal-plus"></i> Create Issue Note
    </a>

    <a href="{{ route('preventive-maintenance.create') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-tools"></i> Preventive Maintenance
    </a>
</div>
<!-- Reports Master -->
<button class="btn btn-outline-primary mb-2"
        data-bs-toggle="collapse"
        data-bs-target="#reportsMenu"
        aria-expanded="false"
        aria-controls="reportsMenu">
    <i class="bi bi-bar-chart"></i> Reports ▾
</button>

<div class="collapse" id="reportsMenu">
    <a href="{{ route('reports.simcard') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-sim"></i> SIM Card Report
    </a>

    <a href="{{ route('reports.internet') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-wifi"></i> Internet Service Report
    </a>

    <a href="{{ route('reports.asset-summary') }}" class="btn btn-sm btn-outline-light mb-1">
        <i class="bi bi-collection"></i> Asset Summary Report (Entity-wise)
    </a>
</div>

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