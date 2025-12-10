
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Projects</h3>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">Add Project</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($projects->count())
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Entity</th>
                        <th>Project Manager</th>
                        <th>PC Secretary</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td>{{ $loop->iteration + ($projects->currentPage()-1) * $projects->perPage() }}</td>
                            <td>{{ $project->project_id }}</td>
                            <td>{{ $project->project_name }}</td>
                            <td>{{ $project->entity ?? '-' }}</td>
                            <td>{{ $project->project_manager ?? '-' }}</td>
                            <td>{{ $project->pc_secretary ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>

                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline-block"
                                      onsubmit="return confirm('Delete project {{ addslashes($project->project_name) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} projects
            </div>
            <div>
                {{ $projects->links() }}
            </div>
        </div>
    @else
        <div class="alert alert-info">No projects found. <a href="{{ route('projects.create') }}">Create one</a>.</div>
    @endif
</div>
@endsection