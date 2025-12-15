@extends('layouts.app')

@section('title', 'Internet Services Management')

@section('content')
<div class="container-fluid master-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-wifi me-2"></i>Internet Services Management</h2>
                <p>Manage internet service subscriptions and accounts</p>
            </div>
            <a href="{{ route('internet-services.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Internet Service
            </a>
        </div>
    </div>
    
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
    
    @if($internetServices->count() > 0)
        <div class="master-table-card">
            <div class="card-header">
                <h5 style="color: white; margin: 0;"><i class="bi bi-list-ul me-2"></i>All Internet Services</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project</th>
                                <th>Entity</th>
                                <th>Service Type</th>
                                <th>Account No.</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Person in Charge</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($internetServices as $index => $service)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $service->project_name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">ID: {{ $service->project_id ?? ($service->project->id ?? 'N/A') }}</small>
                                    </td>
                                    <td>{{ $service->entity ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($service->service_type ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($service->account_number)
                                            <span class="text-danger fw-semibold">{{ $service->account_number }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->service_start_date->format('d-m-Y') }}</td>
                                    <td>
                                        @if($service->service_end_date)
                                            {{ $service->service_end_date->format('d-m-Y') }}
                                        @else
                                            <span class="text-muted">Ongoing</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->person_in_charge ?? 'N/A' }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $service->status ?? 'active' }}">
                                            {{ ucfirst($service->status ?? 'Active') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('internet-services.edit', $service->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('internet-services.destroy', $service->id) }}" 
                                              method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this internet service?')" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="background: #f8f9fa; padding: 12px 20px; border-top: 1px solid #dee2e6;">
                <p class="text-muted mb-0" style="font-size: 14px;">
                    Showing {{ $internetServices->count() }} internet service(s)
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="bi bi-wifi-off display-4 d-block mb-3"></i>
            <h4>No Internet Services Found</h4>
            <p class="mb-3">Start by adding your first internet service.</p>
            <a href="{{ route('internet-services.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add First Internet Service
            </a>
        </div>
    @endif
</div>
@endsection
