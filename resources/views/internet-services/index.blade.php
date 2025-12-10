@extends('layouts.app')

@section('title', 'Internet Services Management')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Internet Services Management</h2>
        <div>
            <a href="{{ route('internet-services.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Internet Service
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
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($internetServices->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr style="background-color: var(--primary); color: var(--white);">
                            <th style="color: var(--white); font-weight: 600;">#</th>
                            <th style="color: var(--white); font-weight: 600;">Project</th>
                            <th style="color: var(--white); font-weight: 600;">Entity</th>
                            <th style="color: var(--white); font-weight: 600;">Service Type</th>
                            <th style="color: var(--white); font-weight: 600;">Account No.</th>
                            <th style="color: var(--white); font-weight: 600;">Start Date</th>
                            <th style="color: var(--white); font-weight: 600;">End Date</th>
                            <th style="color: var(--white); font-weight: 600;">Person in Charge</th>
                            <th style="color: var(--white); font-weight: 600;">Status</th>
                            <th style="color: var(--white); font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($internetServices as $index => $service)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $service->project_name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">ID: {{ $service->project_id ?? ($service->project->id ?? 'N/A') }}</small>
                            </td>
                            <td>{{ $service->entity }}</td>
                            <td>
                                <span class="badge" style="background-color: var(--primary); color: var(--white); padding: 6px 12px; border-radius: 20px;">
                                    {{ ucfirst($service->service_type) }}
                                </span>
                            </td>
                            <td>
                                @if($service->account_number)
                                    <span style="color: #DC3545; font-weight: 500;">{{ $service->account_number }}</span>
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
                            <td>{{ $service->person_in_charge }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'suspend' => 'warning',
                                        'closed' => 'secondary'
                                    ];
                                @endphp
                                @if($service->status == 'closed')
                                    <span class="badge" style="background-color: var(--secondary); color: var(--primary); padding: 6px 12px; border-radius: 20px; font-weight: 500;">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                @else
                                    <span class="badge bg-{{ $statusColors[$service->status] ?? 'secondary' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('internet-services.edit', $service->id) }}" 
                                       class="btn btn-sm" style="background-color: #FFC107; color: #000; border: none;" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('internet-services.destroy', $service->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this internet service?')" 
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-3 py-2" style="background-color: var(--bg-light); border-top: 1px solid var(--border-light);">
                <p class="text-muted mb-0" style="font-size: 0.875rem;">
                    Showing {{ $internetServices->count() }} internet service(s)
                </p>
            </div>
            
            @else
            <div class="text-center py-5 px-3">
                <i class="bi bi-wifi-off display-1 text-muted"></i>
                <h4 class="mt-3">No Internet Services Found</h4>
                <p class="text-muted">Start by adding your first internet service.</p>
                <a href="{{ route('internet-services.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i> Add First Internet Service
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection