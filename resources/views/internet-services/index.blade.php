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
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table align-middle mb-0" style="background-color: #FFFFFF; border-collapse: collapse; width: 100%; min-width: 1200px; table-layout: auto;">
                    <thead>
                        <tr style="background-color: #1F2A44; color: #FFFFFF;">
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 50px; width: 50px; text-align: center;">#</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 150px; text-align: left;">Project</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 120px; text-align: left;">Entity</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 120px; text-align: left;">Service Type</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 120px; text-align: left;">Account No.</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 110px; text-align: left;">Start Date</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 110px; text-align: left;">End Date</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 130px; text-align: left;">Person in Charge</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 100px; text-align: left;">Status</th>
                            <th style="color: #FFFFFF !important; font-weight: 600; padding: 12px; border: none; min-width: 120px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($internetServices as $index => $service)
                        <tr style="background-color: {{ $index % 2 == 0 ? '#FFFFFF' : '#F1F3F5' }}; color: #1F2A44; border-bottom: 1px solid #E5E7EB;">
                            <td style="padding: 12px; color: #1F2A44 !important; text-align: center; font-weight: 500;">{{ $index + 1 }}</td>
                            <td style="padding: 12px; color: #1F2A44 !important; text-align: left;">
                                <strong style="color: #1F2A44 !important;">{{ $service->project_name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted" style="color: #6c757d !important;">ID: {{ $service->project_id ?? ($service->project->id ?? 'N/A') }}</small>
                            </td>
                            <td style="padding: 12px; color: #1F2A44 !important; text-align: left; font-weight: 500;">{{ $service->entity ?? 'N/A' }}</td>
                            <td style="padding: 12px; text-align: left;">
                                <span class="badge" style="background-color: #1F2A44 !important; color: #FFFFFF !important; padding: 6px 12px; border-radius: 20px; display: inline-block; font-weight: 500;">
                                    {{ ucfirst($service->service_type ?? 'N/A') }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: left;">
                                @if($service->account_number)
                                    <span style="color: #DC3545 !important; font-weight: 500;">{{ $service->account_number }}</span>
                                @else
                                    <span class="text-muted" style="color: #6c757d !important;">N/A</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #1F2A44 !important; text-align: left;">{{ $service->service_start_date->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #1F2A44 !important; text-align: left;">
                                @if($service->service_end_date)
                                    {{ $service->service_end_date->format('d-m-Y') }}
                                @else
                                    <span class="text-muted" style="color: #6c757d !important;">Ongoing</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #1F2A44 !important; text-align: left;">{{ $service->person_in_charge ?? 'N/A' }}</td>
                            <td style="padding: 12px; text-align: left;">
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'suspend' => 'warning',
                                        'closed' => 'secondary'
                                    ];
                                @endphp
                                @if($service->status == 'closed')
                                    <span class="badge" style="background-color: #C6A87D !important; color: #1F2A44 !important; padding: 6px 12px; border-radius: 20px; font-weight: 500; display: inline-block;">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                @else
                                    <span class="badge bg-{{ $statusColors[$service->status] ?? 'secondary' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div class="btn-group" role="group" style="display: inline-flex; gap: 5px;">
                                    <a href="{{ route('internet-services.edit', $service->id) }}" 
                                       class="btn btn-sm" style="background-color: #FFC107 !important; color: #000000 !important; border: none; padding: 6px 10px; display: inline-block; min-width: 35px;" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('internet-services.destroy', $service->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this internet service?')" 
                                                title="Delete"
                                                style="padding: 6px 10px; display: inline-block; min-width: 35px; background-color: #DC3545 !important; color: #FFFFFF !important; border: none;">
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
            
            <div class="px-3 py-2" style="background-color: #F7F8FA; border-top: 1px solid #E5E7EB;">
                <p class="text-muted mb-0" style="font-size: 0.875rem; color: #6c757d;">
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