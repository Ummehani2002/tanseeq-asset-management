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
        $validated = $request->validate([
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'system_code' => 'nullable|string|max:255',
            'printer_code' => 'nullable|string|max:255',
            'issued_date' => 'nullable|date',
            'software_installed' => 'nullable|string',
            'items' => 'nullable|array',
            'user_signature' => 'nullable|string',
            'manager_signature' => 'nullable|string',
        ]);

        $validated['items'] = $request->input('items', []);

        // SAVE SIGNATURE FUNCTION
        $validated['user_signature'] = $this->saveSignature($request->user_signature);
        $validated['manager_signature'] = $this->saveSignature($request->manager_signature);
        IssueNote::create($validated);

        return redirect()->route('issue-note.create')
            ->with('success', 'Issue note saved successfully!');
    }

    private function saveSignature($signature)
    {
        if (!$signature) return null;

        $folderPath = storage_path('app/public/signatures/');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $image_parts = explode(";base64,", $signature);
        $image_base64 = base64_decode($image_parts[1]);

        $fileName = 'signature_' . uniqid() . '.png';
        $filePath = $folderPath . $fileName;

        file_put_contents($filePath, $image_base64);

        return 'signatures/' . $fileName;
    }

    public function getEmployeeDetails($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        return response()->json([
            'name' => $employee->name ?? $employee->entity_name,
            'department' => $employee->department ?? 'N/A',
            'entity_name' => $employee->entity_name ?? 'N/A',
            'location' => $employee->location ?? 'N/A',
        ]);
    }
}
