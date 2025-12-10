<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\AssetCategory;
use App\Models\CategoryFeature; // for features
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class AssetController extends Controller
{
    public function index()
{
        $categories = AssetCategory::all();
  $assets = Asset::with(['category', 'brand', 'featureValues.feature'])->get();
return view('assets.index', compact('assets'));

}
public function create()
{
    $lastAsset = \App\Models\Asset::orderBy('id', 'desc')->first();
    $autoAssetId = $lastAsset ? 'AST' . str_pad($lastAsset->id + 1, 5, '0', STR_PAD_LEFT) : 'AST00001';
    $categories = \App\Models\AssetCategory::all();

    // Load all assets with category and brand to show in table
    $assets = \App\Models\Asset::with(['category', 'brand'])->get();

    return view('assets.create', compact('autoAssetId', 'categories', 'assets'));
}

public function getFeaturesByBrand($brandId)
{
    $features = \App\Models\CategoryFeature::where('brand_id', $brandId)->get();
    return response()->json($features);
}

   public function assetsByCategory($id)
{
    $category = AssetCategory::findOrFail($id);
    $assets = Asset::with('category', 'brand')
                ->where('asset_category_id', $id)
                ->get();

    return view('assets.by_category', compact('category', 'assets'));
}

public function store(Request $request)
{
    try {
        $request->merge([
            'expiry_date' => \Carbon\Carbon::parse($request->warranty_start)->addYear()->format('Y-m-d'),
        ]);

        $request->validate([
            'asset_id' => 'required|unique:assets,asset_id',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'purchase_date' => 'required|date',
            'warranty_start' => 'required|date',
            'warranty_years' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date',
            'po_number' => 'nullable|string',
            'serial_number' => 'required|string|max:100', // validate serial number
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'features' => 'array',
            'features.*' => 'nullable|string',
        ]);

        // Save the invoice
        $invoicePath = $request->file('invoice')->store('invoices', 'public');

        // Create the asset
        $asset = Asset::create([
            'asset_id' => $request->asset_id,
            'asset_category_id' => $request->asset_category_id,
            'brand_id' => $request->brand_id,
            'purchase_date' => $request->purchase_date,
            'warranty_start' => $request->warranty_start,
            'warranty_years' => $request->warranty_years,
            'expiry_date' => $request->expiry_date,
            'po_number' => $request->po_number,
            'serial_number' => $request->serial_number,
            'invoice_path' => $invoicePath,
]);

        // Save features if provided
        if ($request->has('features')) {
            foreach ($request->features as $featureId => $value) {
                \DB::table('category_feature_values')->insert([
                    'asset_id' => $asset->id,
                    'category_feature_id' => $featureId,
                    'feature_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

     return redirect()->back()->with('success', 'Asset saved successfully!');

    } catch (\Throwable $e) {
        Log::error('Asset save error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to save asset. Please check logs.');
    }
}
public function locationAssets(Request $request)
{
    $locationName = $request->input('location_name');
    if (!$locationName) {
        return back()->with('error', 'No location name provided.');
    }

    // Find location by name (exact match or use 'like' for partial)
    $location = \App\Models\Location::where('location_name', $locationName)->first();

    if (!$location) {
        return back()->with('error', 'Location not found.');
    }

    // Get asset IDs assigned to this location (in asset_transactions table)
    $assetIds = \App\Models\AssetTransaction::where('location_id', $location->id)
        ->pluck('asset_id')
        ->unique();

    // Get assets with these IDs
    $assets = \App\Models\Asset::whereIn('id', $assetIds)
        ->with(['category', 'brand', 'latestTransaction'])
        ->get();

    return view('assets.location_assets', compact('assets'));
}
public function showRepairForm(Request $request)
{
    $employee = \App\Models\Employee::findOrFail($request->employee);
    $asset = \App\Models\Asset::findOrFail($request->asset);

    return view('asset_transactions.repair_form', compact('employee', 'asset'));
}
public function saveRepair(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'asset_id' => 'required|exists:assets,id',
        'receive_date' => 'required|date',
        'repair_cost' => 'required|numeric',
        'repair_vendor' => 'required|string',
        'repair_type' => 'required|string',
        'remarks' => 'nullable|string',
    ]);

    \App\Models\AssetTransaction::create([
        'transaction_type' => 'repair',
        'employee_id' => $request->employee_id,
        'asset_id' => $request->asset_id,
        'receive_date' => $request->receive_date,
        'repair_cost' => $request->repair_cost,
        'repair_vendor' => $request->repair_vendor,
        'repair_type' => $request->repair_type,
        'remarks' => $request->remarks,
    ]);

    return redirect()->route('asset-transactions.index')->with('success', 'Repair record saved.');
}
public function getAssetDetails($assetId)
{
    // Load asset with related employee and project (adjust relationships)
    $asset = Asset::with(['brand', 'employee', 'project'])->find($assetId);

    if (!$asset) {
        return response()->json(['error' => 'Asset not found'], 404);
    }

    return response()->json([
        'serial_number' => $asset->serial_number,
        'brand' => $asset->brand->name ?? 'N/A',
        'employee_name' => $asset->employee->name ?? null,
        'project_name' => $asset->project->project_name ?? null,
        'invoice' => $asset->invoice_path ?? null,
    ]);
}


public function filter(Request $request)
{
    $categories = AssetCategory::all();
    $categoryId = $request->get('category_id');
    $assets = Asset::with('assetCategory')
                ->when($categoryId, function ($query) use ($categoryId) {
                    return $query->where('asset_category_id', $categoryId);
                })
                ->get();

    return view('assets.index', compact('assets', 'categories', 'categoryId'));
}
public function getFullDetails($id)
{
    $asset = Asset::with('assetCategory', 'employee', 'project')->find($id);
    return response()->json([
        'asset' => $asset,
        'invoice' => $asset->invoice_path, // assuming saved in DB
        'employee' => $asset->employee,
        'project' => $asset->project,
    ]);
}
public function getAssetsByEmployee($id)
{
    $employee = \App\Models\Employee::with(['assetTransactions.asset.category', 'assetTransactions.asset.brand', 'assetTransactions.location'])
        ->find($id);

    if (!$employee) {
        return response()->json([]);
    }

    $assets = $employee->assetTransactions->map(function ($txn) {
        return [
            'asset_id' => $txn->asset->asset_id ?? '-',
            'category' => $txn->asset->category->category_name ?? '-',
            'brand' => $txn->asset->brand->name ?? '-',
            'serial_number' => $txn->asset->serial_number ?? '-',
            'po_number' => $txn->asset->po_number ?? '-',
            'location' => $txn->location->location_name ?? '-',
            'issue_date' => $txn->issue_date ?? '-',
            'status' => ucfirst($txn->transaction_type ?? 'N/A'),
        ];
    });

    return response()->json($assets);
}
public function getAssetsByLocation($id)
{
    $location = \App\Models\Location::with(['assets.category', 'assets.brand'])
        ->find($id);
    if (!$location) {
        return response()->json([]);
    }

    $assets = $location->assets->map(function ($asset) {
        return [
            'asset_id' => $asset->asset_id ?? '-',
            'category' => $asset->category->category_name ?? '-',
            'brand' => $asset->brand->name ?? '-',
            'serial_number' => $asset->serial_number ?? '-',
            'po_number' => $asset->po_number ?? '-',
            'purchase_date' => $asset->purchase_date ?? '-',
            'expiry_date' => $asset->expiry_date ?? '-',
        ];
    });

    return response()->json($assets);
}




}

