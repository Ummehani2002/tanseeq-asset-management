<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\AssetAssigned;
use Illuminate\Support\Facades\Mail;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\SystemMaintenance;

class AssetTransactionController extends Controller
{

public function create()
{
    $assets = \App\Models\Asset::with('assetCategory')->get();
    $employees = \App\Models\Employee::all();
    $transaction = null; // or new AssetTransaction();
        
    $locations = \App\Models\Location::all(); 
    return view('asset_transactions.create', compact('assets', 'employees', 'transaction', 'locations'));
}
    public function showMaintenanceForm(Request $request)
    {
        $asset = Asset::with('assetCategory')->findOrFail($request->asset_id);
        $employee = Employee::findOrFail($request->employee_id);
        $invoice = $asset->invoice ?? null; // Adjust if your invoice field is named differently
        return view('asset_transactions.system_maintenance', compact('asset', 'employee', 'invoice'));
    }

    public function saveMaintenance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'asset_id' => 'required|exists:assets,id',
            'assigned_to' => 'required|string',
            'receive_date' => 'required|date',
            'delivery_date' => 'required|date',
            'repair_type' => 'required|string',
            'maintenance_summary' => 'nullable|string',
        ]);

        // Save maintenance record (use your own model/table as needed)
        \DB::table('system_maintenances')->insert([
            'employee_id' => $request->employee_id,
            'asset_id' => $request->asset_id,
            'assigned_to' => $request->assigned_to,
            'receive_date' => $request->receive_date,
            'delivery_date' => $request->delivery_date,
            'repair_type' => $request->repair_type,
            'maintenance_summary' => $request->maintenance_summary,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

       
return redirect()->back()->with('success', 'Transaction successful and email sent!');
    }

public function store(Request $request)
{
    $request->validate([
        'asset_id' => 'required|exists:assets,id',
        'transaction_type' => 'required|in:assign,return,system_maintenance',
    ]);

    $asset = \App\Models\Asset::with('assetCategory')->findOrFail($request->asset_id);
    $category = $asset->assetCategory->category_name ?? '';

    $transaction = new \App\Models\AssetTransaction();
    $transaction->asset_id = $request->asset_id;
    $transaction->transaction_type = $request->transaction_type;

    if ($category && strtolower($category) === 'laptop') {
        $transaction->employee_id = $request->employee_id;
    } elseif ($category && strtolower($category) === 'printer') {
        $transaction->project_name = $request->project_name;
    }

    if ($request->transaction_type === 'assign') {
        $transaction->location = $request->location;
        $transaction->issue_date = $request->issue_date;
        if ($request->hasFile('asset_image')) {
            $transaction->asset_image = $request->file('asset_image')->store('assets', 'public');
        }
        $transaction->remarks = $request->remarks;
    } elseif ($request->transaction_type === 'return') {
        $transaction->return_date = $request->return_date;
        // Optionally send email to employee here
    }

    $transaction->save();

    // --- Mail sending code here ---
    $employee = Employee::find($request->employee_id);
    $asset = Asset::find($request->asset_id);

    if ($employee && $asset) {
        Mail::to($employee->email)->send(new AssetAssigned($employee, $asset));
    }
    // --- End mail sending code ---

    return redirect()->route('asset-transactions.create')->with('success', 'Asset transaction saved and mail sent!');
}


public function edit($id)
{
    $transaction = \App\Models\AssetTransaction::findOrFail($id);
    $assets = \App\Models\Asset::with('assetCategory')->get();
    $employees = \App\Models\Employee::all();
      $locations = \App\Models\Location::all(); // <-- ADD THIS
    return view('asset_transactions.edit', compact('transaction', 'assets', 'employees', 'locations'));
}
}