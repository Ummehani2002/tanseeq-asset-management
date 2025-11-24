<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TimeManagement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobDelayAlertMail;


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
    return view('time_management.create', compact('employees'));
}

public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'project_name' => 'required|string',
        'job_card_date' => 'required|date',
        'standard_man_hours' => 'required|numeric|min:0',
    ]);

    $employee = \App\Models\Employee::find($request->employee_id);

    // ✅ Fetch the latest ticket_number instead of using count
    $lastRecord = \App\Models\TimeManagement::orderBy('id', 'desc')->first();
    $lastNumber = $lastRecord ? intval(substr($lastRecord->ticket_number, 4)) : 0;
    $newNumber = $lastNumber + 1;

    $ticketNumber = 'TCKT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    \App\Models\TimeManagement::create([
        'ticket_number' => $ticketNumber,
        'employee_id' => $employee->id,
        'employee_name' => $employee->name,
        'project_name' => $request->project_name,
        'job_card_date' => $request->job_card_date,
        'standard_man_hours' => $request->standard_man_hours,
        'start_time' => \Carbon\Carbon::now(),
        'status' => 'in_progress',
    ]);

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
if ($delayedDays > 0 || $actualHours > $record->standard_man_hours) {
    // Send email only when delay or overtime happens
    Mail::to('manager@example.com')->send(new JobDelayAlertMail($record));
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
