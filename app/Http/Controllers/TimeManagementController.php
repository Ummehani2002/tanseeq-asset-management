<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TimeManagement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobDelayAlertMail;
use App\Mail\TaskAssignedMail;


class TimeManagementController extends Controller
{
    public function index()
    {
        $records = TimeManagement::orderBy('id', 'desc')->get();
        return view('time_management.index', compact('records'));
    }

public function create()
{
    $employees = \App\Models\Employee::all();
    $projects = \App\Models\Project::all();
    return view('time_management.create', compact('employees', 'projects'));
}

public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'project_id' => 'nullable|exists:projects,id',
        'project_name' => 'required|string',
        'job_card_date' => 'required|date',
        'standard_man_hours' => 'required|numeric|min:0',
    ]);

    $employee = \App\Models\Employee::find($request->employee_id);
    
    // If project_id is provided, get project name from database
    $projectName = $request->project_name;
    if ($request->project_id) {
        $project = \App\Models\Project::find($request->project_id);
        if ($project) {
            $projectName = $project->project_name;
        }
    }

    // ✅ Fetch the latest ticket_number instead of using count
    $lastRecord = \App\Models\TimeManagement::orderBy('id', 'desc')->first();
    $lastNumber = $lastRecord ? intval(substr($lastRecord->ticket_number, 4)) : 0;
    $newNumber = $lastNumber + 1;

    $ticketNumber = 'TCKT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    $timeRecord = \App\Models\TimeManagement::create([
        'ticket_number' => $ticketNumber,
        'employee_id' => $employee->id,
        'employee_name' => $employee->name,
        'project_name' => $projectName,
        'job_card_date' => $request->job_card_date,
        'standard_man_hours' => $request->standard_man_hours,
        'start_time' => \Carbon\Carbon::now(),
        'status' => 'in_progress',
    ]);

    // Send email to employee when task is assigned
    if ($employee->email) {
        try {
            Mail::to($employee->email)->send(new TaskAssignedMail($timeRecord));
            \Log::info('Task assignment email sent to: ' . $employee->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send task assignment email: ' . $e->getMessage());
        }
    }

    return redirect()->route('time.index')->with('success', 'Job Card Created Successfully!');
}




    public function edit($id)
    {
        $record = TimeManagement::findOrFail($id);
        return view('time_management.edit', compact('record'));
    }

    public function update(Request $request, $id)
{
    $record = TimeManagement::findOrFail($id);

    // Auto set end_time = now (no manual input)
    $endTime = Carbon::now();
    $start = Carbon::parse($record->start_time);

    $record->end_time = $endTime;
    $record->status = 'completed';

    // Calculate duration safely
    $actualHours = max(0.01, $endTime->diffInMinutes($start) / 60); // avoid division by zero

    // Calculate performance (cap between 0 and 100)
    if ($record->standard_man_hours > 0) {
        $performance = ($record->standard_man_hours / $actualHours) * 100;
        $performance = max(0, min(100, round($performance, 2)));
    } else {
        $performance = 0;
    }

    // Calculate delayed days (never negative)
    $expectedEndDate = Carbon::parse($record->job_card_date)->addDay();
    $delayedDays = max(0, $endTime->diffInDays($expectedEndDate, false));

    $record->performance_percent = $performance;
    $record->delayed_days = $delayedDays;
    $record->delay_reason = $request->delay_reason ?? null;

    $record->save();
    
    // Send email to employee if delayed or overtime
    if ($delayedDays > 0 || $actualHours > $record->standard_man_hours) {
        $employee = \App\Models\Employee::find($record->employee_id);
        if ($employee && $employee->email) {
            try {
                Mail::to($employee->email)->send(new JobDelayAlertMail($record));
                \Log::info('Delay alert email sent to employee: ' . $employee->email);
            } catch (\Exception $e) {
                \Log::error('Failed to send delay alert email: ' . $e->getMessage());
            }
        }
    }

    return redirect()->route('time.index')->with('success', 'Job Card Completed Successfully!');
}


    public function destroy($id)
    {
        $record = TimeManagement::findOrFail($id);
        $record->delete();
        return redirect()->route('time.index')->with('success', 'Job Card Deleted Successfully!');
    }
}
