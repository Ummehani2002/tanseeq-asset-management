<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetTransaction;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Project;
use Illuminate\Support\Facades\Mail;
use App\Mail\AssetAssigned;
use Illuminate\Validation\ValidationException;

class AssetTransactionController extends Controller
{
    public function index()
    {
        $transactions = AssetTransaction::with(['asset', 'employee', 'location'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('asset_transactions.index', compact('transactions'));
    }

    public function create()
    {
        $categories = \App\Models\AssetCategory::all();
        $assets = Asset::with('assetCategory')->get();
        $employees = Employee::all();
        $locations = Location::all();
        $projects = \App\Models\Project::all();

        return view('asset_transactions.create', compact('categories', 'assets', 'employees', 'locations', 'projects'));
    }

    public function edit($id)
    {
        $transaction = AssetTransaction::with(['asset.assetCategory', 'employee'])->findOrFail($id);
        $categories = \App\Models\AssetCategory::all();
        $assets = Asset::with('assetCategory')->get();
        $employees = Employee::all();
        $locations = Location::all();
        $projects = \App\Models\Project::all();

        return view('asset_transactions.create', compact('transaction', 'categories', 'assets', 'employees', 'locations', 'projects'));
    }

    public function getAssetsByCategory($categoryId)
    {
        $assets = Asset::with('assetCategory')
            ->where('asset_category_id', $categoryId)
            ->get()
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'asset_id' => $asset->asset_id,
                    'serial_number' => $asset->serial_number,
                    'status' => $asset->status ?? 'available',
                    'category_name' => $asset->assetCategory->category_name ?? 'N/A'
                ];
            });

        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'asset_id' => 'required|exists:assets,id',
            'transaction_type' => 'required|in:assign,return,system_maintenance',
            'employee_id' => 'nullable|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'project_name' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'receive_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
        ]);

        $asset = Asset::with('assetCategory')->findOrFail($request->asset_id);
        $category = $asset->assetCategory;
        $latest = $asset->latestTransaction;
        
        // Validate assignment can only be done when asset is available or under maintenance (reassignment after maintenance)
        if (
            $request->transaction_type === 'assign' &&
            !in_array($asset->status, ['available', 'under_maintenance'])
        ) {
            throw ValidationException::withMessages([
                'asset_id' => 'Asset is not available for assignment. Current status: ' . ucfirst($asset->status ?? 'unknown'),
            ]);
        }

        // Validate category-specific requirements
        if ($request->transaction_type === 'assign') {
            if (strtolower($category->category_name) === 'laptop' && !$request->employee_id) {
                throw ValidationException::withMessages([
                    'employee_id' => 'Employee is required for laptop assignment.',
                ]);
            }
            if (strtolower($category->category_name) === 'printer' && !$request->project_name) {
                throw ValidationException::withMessages([
                    'project_name' => 'Project name is required for printer assignment.',
                ]);
            }
        }
        
        $data = $this->resolveAssignment($asset, $latest, $request, $category);
        $status = $this->getStatusForTransaction($request->transaction_type, $latest);
        
        // Get employee for email BEFORE creating transaction (especially for return/maintenance)
        $employeeForEmail = null;
        
        // For assign - use employee from request
        if ($request->transaction_type === 'assign' && $request->employee_id) {
            $employeeForEmail = Employee::find($request->employee_id);
        }
        // For return/maintenance - get from latest assignment
        elseif (in_array($request->transaction_type, ['return', 'system_maintenance'])) {
            if ($latest && $latest->employee_id) {
                $employeeForEmail = Employee::find($latest->employee_id);
            }
            // If not found in latest, try to find from any previous assign transaction
            if (!$employeeForEmail) {
                $previousAssign = AssetTransaction::where('asset_id', $asset->id)
                    ->where('transaction_type', 'assign')
                    ->whereNotNull('employee_id')
                    ->latest()
                    ->first();
                if ($previousAssign) {
                    $employeeForEmail = Employee::find($previousAssign->employee_id);
                }
            }
        }
        
        $transaction = AssetTransaction::create(array_merge([
            'asset_id' => $asset->id,
            'transaction_type' => $request->transaction_type,
            'status' => $status,
            'issue_date' => $request->issue_date,
            'return_date' => $request->return_date,
            'receive_date' => $request->receive_date,
            'delivery_date' => $request->delivery_date,
            'location_id' => $request->location_id,
            'project_name' => $request->project_name,
        ], $data));
        
        $asset->update(['status' => $status]);
        
        // Temporarily set employee_id for email sending if we found one
        if ($employeeForEmail) {
            $transaction->employee_id = $employeeForEmail->id;
            $transaction->save(); // Save temporarily for email
        }
        
        $this->sendAssetEmail($transaction);
        
        // For return transactions, clear employee_id after email is sent
        if ($request->transaction_type === 'return' && $employeeForEmail) {
            $transaction->employee_id = null;
            $transaction->save();
        }
        

        return redirect()->route('asset-transactions.index')->with('success', 'Transaction saved successfully!');
    }

    public function update(Request $request, $id)
    {
        $transaction = AssetTransaction::findOrFail($id);

        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'transaction_type' => 'required|in:assign,return,system_maintenance',
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'nullable|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'project_name' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'receive_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
        ]);

        $asset = Asset::with('assetCategory')->findOrFail($request->asset_id);
        $category = $asset->assetCategory;
        $latest = $asset->latestTransaction;

        // Validate assignment can only be done when asset is available or under maintenance (reassignment after maintenance)
        if (
            $request->transaction_type === 'assign' &&
            !in_array($asset->status, ['available', 'under_maintenance'])
        ) {
            throw ValidationException::withMessages([
                'asset_id' => 'Asset is not available for assignment. Current status: ' . ucfirst($asset->status ?? 'unknown'),
            ]);
        }

        $data = $this->resolveAssignment($asset, $latest, $request, $category);
        $status = $this->getStatusForTransaction($request->transaction_type, $latest);

        $transaction->update(array_merge([
            'asset_id' => $asset->id,
            'transaction_type' => $request->transaction_type,
            'location_id' => $request->location_id,
            'project_name' => $request->project_name,
            'issue_date' => $request->issue_date,
            'return_date' => $request->return_date,
            'receive_date' => $request->receive_date,
            'delivery_date' => $request->delivery_date,
            'status' => $status,
        ], $data));

        $asset->status = $status;
        $asset->save();

        // Send email for all transaction types
        $this->sendAssetEmail($transaction);

        return redirect()->route('asset-transactions.index')->with('success', 'Transaction updated successfully!');
    }

    private function getStatusForTransaction($type, $latest = null)
    {
        // If returning from maintenance, status should be 'assigned' not 'available'
        if ($type === 'return' && $latest && $latest->transaction_type === 'system_maintenance') {
            return 'assigned';
        }
        
        return match ($type) {
            'assign' => 'assigned',
            'return' => 'available',
            'system_maintenance' => 'under_maintenance',
            default => 'available',
        };
    }
    
    private function resolveAssignment($asset, $latest, $request, $category)
{
    $categoryName = strtolower($category->category_name ?? '');

    if ($request->transaction_type === 'assign') {
        if ($categoryName === 'laptop') {
            return [
                'assigned_to_type' => 'employee',
                'employee_id' => $request->employee_id,
                'project_name' => null,
            ];
        }

        if ($categoryName === 'printer') {
            return [
                'assigned_to_type' => 'project',
                'employee_id' => null,
                'project_name' => $request->project_name,
            ];
        }
    }

    if ($request->transaction_type === 'system_maintenance') {
        // For maintenance, keep the same employee/project from the latest assignment
        // This ensures when returned from maintenance, it goes back to the same employee
        if ($latest && $latest->transaction_type === 'assign') {
            return [
                'assigned_to_type' => $latest->assigned_to_type ?? 'employee',
                'employee_id' => $latest->employee_id,
                'project_name' => $latest->project_name,
            ];
        }
        // If no previous assignment, use current request values
        return [
            'assigned_to_type' => $categoryName === 'laptop' ? 'employee' : 'project',
            'employee_id' => $request->employee_id,
            'project_name' => $request->project_name,
        ];
    }

    if ($request->transaction_type === 'return') {
        // Check if this is a return from maintenance - if so, restore to previous assignment
        if ($latest && $latest->transaction_type === 'system_maintenance') {
            // Find the assignment before maintenance
            $beforeMaintenance = AssetTransaction::where('asset_id', $asset->id)
                ->where('transaction_type', 'assign')
                ->where('id', '<', $latest->id)
                ->latest()
                ->first();
            
            if ($beforeMaintenance) {
                // Return from maintenance - restore to same employee/project
                return [
                    'assigned_to_type' => $beforeMaintenance->assigned_to_type,
                    'employee_id' => $beforeMaintenance->employee_id,
                    'project_name' => $beforeMaintenance->project_name,
                ];
            }
        }
        
        // Regular return - clears assignment and makes asset available
        return [
            'assigned_to_type' => null,
            'employee_id' => null,
            'project_name' => null,
        ];
    }

    return [];
}


private function sendAssetEmail($transaction)
{
    // Send email for ALL transaction types (assign, return, maintenance)
    $employee = null;
    
    // Reload transaction with relationships
    $transaction = AssetTransaction::with(['asset.assetCategory', 'employee'])->find($transaction->id);
    
    if (!$transaction) {
        \Log::error('Transaction not found for email sending');
        return;
    }
    
    \Log::info('=== Email Sending Debug ===');
    \Log::info('Transaction ID: ' . $transaction->id);
    \Log::info('Transaction Type: ' . $transaction->transaction_type);
    \Log::info('Asset ID: ' . $transaction->asset_id);
    \Log::info('Employee ID in transaction: ' . ($transaction->employee_id ?? 'null'));
    
    // Try to get employee from current transaction
    if ($transaction->employee_id) {
        $employee = Employee::find($transaction->employee_id);
        if ($employee) {
            \Log::info('Found employee from transaction: ' . $employee->name . ' (Email: ' . ($employee->email ?? 'NO EMAIL') . ')');
        }
    }
    
    // For maintenance or return, get employee from previous assignment if not found
    if (!$employee && in_array($transaction->transaction_type, ['system_maintenance', 'return'])) {
        $latestAssign = AssetTransaction::where('asset_id', $transaction->asset_id)
            ->where('transaction_type', 'assign')
            ->whereNotNull('employee_id')
            ->where('id', '!=', $transaction->id) // Exclude current transaction
            ->latest()
            ->first();
            
        if ($latestAssign) {
            $employee = Employee::find($latestAssign->employee_id);
            if ($employee) {
                \Log::info('Found employee from previous assignment: ' . $employee->name . ' (Email: ' . ($employee->email ?? 'NO EMAIL') . ')');
            }
        } else {
            \Log::warning('No previous assignment found for asset ID: ' . $transaction->asset_id);
        }
    }
    
    // Send email if employee exists and has email
    if ($employee) {
        if (empty($employee->email)) {
            \Log::warning('Employee found but no email address: ' . $employee->name . ' (ID: ' . $employee->id . ')');
            return;
        }
        
        try {
            \Log::info('Attempting to send email to: ' . $employee->email);
            \Log::info('Mail driver: ' . config('mail.default'));
            \Log::info('Mail from: ' . config('mail.from.address'));
            
            Mail::to($employee->email)->send(
                new AssetAssigned(
                    $transaction->asset,
                    $employee,
                    $transaction
                )
            );
            
            \Log::info('✓ SUCCESS: Asset transaction email sent to: ' . $employee->email . ' for transaction: ' . $transaction->transaction_type);
        } catch (\Exception $e) {
            // Log error but don't fail the transaction
            \Log::error('✗ FAILED to send asset transaction email to ' . $employee->email);
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
        }
    } else {
        \Log::warning('No employee found for transaction. Type: ' . $transaction->transaction_type . ', Asset ID: ' . $transaction->asset_id);
    }
    
    \Log::info('=== End Email Debug ===');
}


    public function destroy($id)
    {
        $transaction = AssetTransaction::findOrFail($id);
        $transaction->delete();
        return redirect()->route('asset-transactions.index')->with('success', 'Transaction deleted.');
    }

    public function getLatestEmployee(Asset $asset)
    {
        $latest = $asset->latestTransaction;
        return response()->json([
            'employee_id' => $latest && $latest->transaction_type === 'assign' ? $latest->employee_id : null
        ]);
    }

    public function getAssetDetails($assetId)
    {
        $asset = Asset::with(['assetCategory', 'latestTransaction.employee'])->findOrFail($assetId);
        
        $latestTransaction = $asset->latestTransaction;
        $status = $asset->status ?? 'available';
        
        $data = [
            'asset_id' => $asset->asset_id,
            'serial_number' => $asset->serial_number,
            'category_name' => $asset->assetCategory->category_name ?? 'N/A',
            'category_id' => $asset->asset_category_id,
            'status' => $status,
            'current_employee_id' => null,
            'current_employee_name' => null,
            'current_project_name' => null,
            'current_location_id' => null,
            'available_transactions' => []
        ];

        // Get current assignment details
        if ($latestTransaction) {
            $data['current_employee_id'] = $latestTransaction->employee_id;
            $data['current_employee_name'] = $latestTransaction->employee->name ?? null;
            $data['current_project_name'] = $latestTransaction->project_name;
            $data['current_location_id'] = $latestTransaction->location_id;
        }

        // Determine available transaction types based on status
        if ($status === 'available') {
            $data['available_transactions'] = ['assign'];
        } elseif ($status === 'assigned') {
            $data['available_transactions'] = ['return', 'system_maintenance'];
        } elseif ($status === 'under_maintenance') {
            // Can only return or reassign to same employee
            $data['available_transactions'] = ['return', 'assign'];
            // For maintenance, we need to find the employee before maintenance
            $beforeMaintenance = AssetTransaction::with('employee')
                ->where('asset_id', $asset->id)
                ->where('transaction_type', 'assign')
                ->where('id', '<', $latestTransaction->id)
                ->latest()
                ->first();
            if ($beforeMaintenance) {
                $data['current_employee_id'] = $beforeMaintenance->employee_id;
                $data['current_employee_name'] = $beforeMaintenance->employee->name ?? null;
            }
        }

        return response()->json($data);
    }
}
