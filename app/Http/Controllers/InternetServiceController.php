<?php

namespace App\Http\Controllers;

use App\Models\InternetService;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;

class InternetServiceController extends Controller
{
    // Display all internet services
    public function index()
    {
        $internetServices = InternetService::with(['project', 'personInCharge', 'projectManager'])
            ->latest()
            ->get();
        
        return view('internet-services.index', compact('internetServices'));
    }


    // Show create form
    public function create()
    {
        $projects = Project::select('id','project_id','project_name','entity')->orderBy('project_id')->get();
        $employees = Employee::orderBy('name')->get();
        
        return view('internet-services.create', compact('projects', 'employees'));
    }


    // Store a new internet service
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_type' => 'required|in:simcard,fixed,service',
            'account_number' => 'nullable|string|max:100',
            'service_start_date' => 'required|date',
            'service_end_date' => 'nullable|date|after:service_start_date',

            // Employee Master ID
            'person_in_charge_id' => 'required|exists:employees,id',
            
            'project_manager' => 'nullable|string|max:255',
            'status' => 'required|in:active,suspend,closed'
        ]);

        // Fetch Project
        $project = Project::findOrFail($validated['project_id']);

        // Fetch Employee
        $emp = Employee::findOrFail($validated['person_in_charge_id']);

        // Auto-fill data
        $validated['project_name'] = $project->project_name;
        $validated['entity'] = $project->entity;
        $validated['person_in_charge'] = $emp->name ?? $emp->entity_name;
        $validated['contact_details'] = $emp->phone ?? 'N/A';

        InternetService::create($validated);

        return redirect()->route('internet-services.index')
            ->with('success', 'Internet service created successfully.');
    }


    // Edit page
    public function edit(InternetService $internetService)
    {
        $projects = Project::select('id','project_id','project_name','entity')->orderBy('project_id')->get();
        $employees = Employee::orderBy('name')->get();

        return view('internet-services.edit', compact('internetService', 'projects', 'employees'));
    }


    // Update record
    public function update(Request $request, InternetService $internetService)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_type' => 'required|in:simcard,fixed,service',
            'account_number' => 'nullable|string|max:100',
            'service_start_date' => 'required|date',
            'service_end_date' => 'nullable|date|after:service_start_date',

            // updated employee ID
            'person_in_charge_id' => 'required|exists:employees,id',

            'project_manager' => 'nullable|string|max:255',
            'status' => 'required|in:active,suspend,closed'
        ]);

        // Fetch Project
        $project = Project::findOrFail($validated['project_id']);

        // Fetch Employee
        $emp = Employee::findOrFail($validated['person_in_charge_id']);

        // Auto fill
        $validated['project_name'] = $project->project_name;
        $validated['entity'] = $project->entity;
        $validated['person_in_charge'] = $emp->name ?? $emp->entity_name;
        $validated['contact_details'] = $emp->phone ?? 'N/A';

        $internetService->update($validated);

        return redirect()->route('internet-services.index')
            ->with('success', 'Internet service updated successfully.');
    }


    // Delete
    public function destroy(InternetService $internetService)
    {
        $internetService->delete();

        return redirect()->route('internet-services.index')
            ->with('success', 'Internet service deleted successfully.');
    }
}
