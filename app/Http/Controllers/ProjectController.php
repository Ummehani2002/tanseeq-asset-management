<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Employee;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderByDesc('created_at')->paginate(20);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $employees = Employee::select('id','name','entity_name')->get();
        $entities = Employee::select('entity_name')->distinct()->pluck('entity_name');
        return view('projects.create', compact('employees','entities'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'project_id'      => 'required|string|max:100|unique:projects,project_id',
            'project_name'    => 'required|string|max:255',
            'entity'          => 'nullable|string|max:255',
            'project_manager' => 'nullable|string|max:255',
            'pc_secretary'    => 'nullable|string|max:255',
            // optional: accept manager_id/pc_secretary_id but store names
            'project_manager_id' => 'nullable|exists:employees,id',
            'pc_secretary_id'    => 'nullable|exists:employees,id',
        ]);

        // if ids provided, convert to names
        if ($request->filled('project_manager_id')) {
            $mgr = Employee::find($request->input('project_manager_id'));
            if ($mgr) $v['project_manager'] = $mgr->name ?? $mgr->entity_name;
        }

        if ($request->filled('pc_secretary_id')) {
            $sec = Employee::find($request->input('pc_secretary_id'));
            if ($sec) $v['pc_secretary'] = $sec->name ?? $sec->entity_name;
        }

        Project::create([
            'project_id' => $v['project_id'],
            'project_name' => $v['project_name'],
            'entity' => $v['entity'] ?? null,
            'project_manager' => $v['project_manager'] ?? null,
            'pc_secretary' => $v['pc_secretary'] ?? null,
        ]);

        return redirect()->route('projects.create')->with('success', 'Project saved successfully.');
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('projects.show', compact('project'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $employees = Employee::select('id','name','entity_name')->get();
        $entities = Employee::select('entity_name')->distinct()->pluck('entity_name');
        return view('projects.edit', compact('project','employees','entities'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $v = $request->validate([
            'project_id'      => 'required|string|max:100|unique:projects,project_id,' . $project->id,
            'project_name'    => 'required|string|max:255',
            'entity'          => 'nullable|string|max:255',
            'project_manager' => 'nullable|string|max:255',
            'pc_secretary'    => 'nullable|string|max:255',
            'project_manager_id' => 'nullable|exists:employees,id',
            'pc_secretary_id'    => 'nullable|exists:employees,id',
        ]);

        if ($request->filled('project_manager_id')) {
            $mgr = Employee::find($request->input('project_manager_id'));
            if ($mgr) $v['project_manager'] = $mgr->name ?? $mgr->entity_name;
        }

        if ($request->filled('pc_secretary_id')) {
            $sec = Employee::find($request->input('pc_secretary_id'));
            if ($sec) $v['pc_secretary'] = $sec->name ?? $sec->entity_name;
        }

        $project->update([
            'project_id' => $v['project_id'],
            'project_name' => $v['project_name'],
            'entity' => $v['entity'] ?? null,
            'project_manager' => $v['project_manager'] ?? null,
            'pc_secretary' => $v['pc_secretary'] ?? null,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
