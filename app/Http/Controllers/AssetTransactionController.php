<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetTransaction;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Location;
use App\Mail\AssetAssigned;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetTransactionController extends Controller
{
    public function create()
    {
        $assets = Asset::all();
        $employees = Employee::all();
        $locations = Location::all();
        return view('asset_transactions.create', compact('assets', 'employees', 'locations'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'transaction_type' => 'required|string',
            'employee_id' => 'required|exists:employees,employee_id',
            'asset_id' => 'required|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'remarks' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'receive_date' => 'nullable|date',
            'received_by' => 'nullable|string',
            'repair_cost' => 'nullable|numeric',
            'repair_vendor' => 'nullable|string',
            'repair_type' => 'nullable|string',
            'image_path' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // handle image upload
            $imagePath = null;
            if ($request->hasFile('image_path')) {
                $file = $request->file('image_path');
                $imagePath = $file->store('asset-transactions', 'public');
            }

            // create transaction
            $trans = AssetTransaction::create([
                'transaction_type' => $v['transaction_type'],
                'employee_id' => $v['employee_id'],
                'asset_id' => $v['asset_id'],
                'location_id' => $v['location_id'] ?? null,
                'remarks' => $v['remarks'] ?? null,
                'issue_date' => $v['issue_date'] ?? null,
                'receive_date' => $v['receive_date'] ?? null,
                'received_by' => $v['received_by'] ?? null,
                'repair_cost' => $v['repair_cost'] ?? 0,
                'repair_vendor' => $v['repair_vendor'] ?? null,
                'repair_type' => $v['repair_type'] ?? null,
                'image_path' => $imagePath
            ]);

            // get asset and employee
            $asset = Asset::find($v['asset_id']);
            $employee = Employee::where('employee_id', $v['employee_id'])->first();

            // send email to employee
            if ($employee && $employee->email) {
                try {
                    Mail::to($employee->email)->send(new AssetAssigned($asset, $employee, $trans));
                } catch (\Exception $mailErr) {
                    Log::warning('Email failed but transaction saved: ' . $mailErr->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('asset-transactions.index')
                ->with('success', 'Asset transaction saved successfully! Email sent to ' . ($employee->name ?? 'employee'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AssetTransaction Store Error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->withInput()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $transactions = AssetTransaction::with('asset', 'employee', 'location')
            ->orderByDesc('created_at')
            ->paginate(25);
        return view('asset_transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = AssetTransaction::with('asset', 'employee', 'location')->findOrFail($id);
        return view('asset_transactions.show', compact('transaction'));
    }
}