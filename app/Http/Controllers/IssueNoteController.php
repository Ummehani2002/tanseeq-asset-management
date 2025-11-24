<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\IssueNote;

class IssueNoteController extends Controller
{
    public function create()
    {
        $employees = Employee::all();
        return view('issue-note.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'system_code' => 'required',
            'issued_date' => 'required',
        ]);

        IssueNote::create($request->all());

        return redirect()->back()->with('success', 'Issue Note Saved Successfully');
    }
}
