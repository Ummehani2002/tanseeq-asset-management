@extends('layouts.app')

@section('content')
<div class="container-fluid master-page">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="bi bi-kanban me-2"></i>Project Master</h2>
        <p>Manage project information and details</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Project
        </a>
    </div>

    @if($projects->count())
        <div class="master-table-card">
            <div class="card-header">
                <h5 style="color: white; margin: 0;"><i class="bi bi-list-ul me-2"></i>All Projects</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project ID</th>
                                <th>Project Name</th>
                                <th>Entity</th>
                                <th>Project Manager</th>
                                <th>PC Secretary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                                <tr>
                                    <td>{{ $loop->iteration + ($projects->currentPage()-1) * $projects->perPage() }}</td>
                                    <td>{{ $project->project_id }}</td>
                                    <td>{{ $project->project_name }}</td>
                                    <td>{{ $project->entity ?? 'N/A' }}</td>
                                    <td>{{ $project->project_manager ?? 'N/A' }}</td>
                                    <td>{{ $project->pc_secretary ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline-block"
                                              onsubmit="return confirm('Delete project {{ addslashes($project->project_name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
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

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div style="font-size: 14px; color: #6c757d;">
                Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} projects
            </div>
            <div>
                {{ $projects->links() }}
            </div>
        </div>
    @else
        <div class="alert alert-info">
            No projects found. <a href="{{ route('projects.create') }}">Create one</a>.
        </div>
    @endif
</div>
@endsection
