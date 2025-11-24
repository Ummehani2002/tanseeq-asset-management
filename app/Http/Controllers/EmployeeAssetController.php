<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Employee;

class EmployeeAssetController extends Controller
{
    // Show the form and results
    public function getAssets(Request $request)
    {
        $assets = null;

        if ($request->has('employee_id')) {
            // Log the input for debugging
            \Log::info('Searching for employee_id: ' . $request->employee_id);

            // Find employee by employee_id (e.g., EMP001)
            $employee = Employee::where('employee_id', $request->employee_id)->first();

            if (!$employee) {
                return back()->with('error', 'Employee not found.');
            }

            // Now fetch the assets using the internal ID
            $assets = Asset::with(['category', 'employee'])
                ->where('employee_id', $employee->id)
                ->get();

            \Log::info('Assets found: ' . $assets->count());
        }

        return view('employee-assets', compact('assets'));
    }
    
public function index(Request $request)
{
    $employee = null;
    if ($request->filled('employee_id')) {
        $employee = Employee::with('assets.assetCategory')->find($request->employee_id);
        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }
    }

    return view('employee-assets', compact('employee'));
}


public function showAssets($id)
{
    $employee = Employee::with('assets.assetCategory')->findOrFail($id);
    return view('employee-assets-single', compact('employee')); // updated view name
}

}
