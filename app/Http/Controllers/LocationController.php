<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Imports\LocationsImport;
use App\Models\Asset;
use App\Models\AssetTransaction;

class LocationController extends Controller
{
        public function index()
    {
        $locations = Location::all();
        return view('location.index', compact('locations'));
    }
  public function store(Request $request)
{
    $request->validate([
        'location_id' => 'required|unique:locations,location_id',
        'location_name' => 'required|string',
        'location_category' => 'nullable|string',
        'location_entity' => 'required|string',
    ]);

    Location::create([
        'location_id' => $request->location_id,
        'location_name' => $request->location_name,
        'location_category' => $request->location_category,
        'location_entity' => $request->location_entity,
    ]);

    return redirect()->route('location-master.index')->with('success', 'Location added successfully.');
}
public function edit($id)
{
    $location = Location::findOrFail($id);
    return view('location.edit', compact('location'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'location_name' => 'required|string|max:255',
    ]);

    $location = Location::findOrFail($id);
    $location->location_name = $request->input('location_name');
    $location->save();

    return redirect()->route('location-master.index')->with('success', 'Location updated successfully.');
}

public function destroy($id)
{
   
    \DB::table('asset_transactions')->where('location_id', $id)->delete();

    Location::destroy($id);
    return redirect()->route('location-master.index')->with('success', 'Location and related asset transactions deleted successfully.');
}

public function showImportForm()
{
    return view('location.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);
    Excel::import(new \App\Imports\LocationsImport, $request->file('file'));
    return back()->with('success', 'Locations imported successfully!');
}

public function autocomplete(Request $request)
{
    $query = trim($request->get('query', ''));
    
    if(empty($query)) {
        return response()->json([]);
    }

    // Search by location_name (starts with first, then contains) or location_id
    $locations = Location::where(function($q) use ($query) {
            $q->where('location_name', 'LIKE', "{$query}%")  // Starts with (priority)
              ->orWhere('location_name', 'LIKE', "%{$query}%") // Contains
              ->orWhere('location_id', 'LIKE', "{$query}%"); // Location ID starts with
        })
        ->orderBy('location_name', 'asc')
        ->take(15)
        ->get(['id', 'location_id', 'location_name', 'location_category']);

    // Sort results: names starting with query first
    $locations = $locations->sortBy(function($location) use ($query) {
        $name = strtolower($location->location_name ?? '');
        $queryLower = strtolower($query);
        
        if(strpos($name, $queryLower) === 0) return 1; // Starts with
        return 2; // Contains
    })->values();

    return response()->json($locations);
}


    // GET /locations/{id}/assets
    public function assets($id)
    {
        // Get asset IDs from latest transactions where location_id matches and type is 'assign'
        $assetIds = AssetTransaction::where('location_id', $id)
            ->where('transaction_type', 'assign')
            ->whereNotNull('asset_id')
            ->select('asset_id')
            ->selectRaw('MAX(id) as max_id')
            ->groupBy('asset_id')
            ->get()
            ->pluck('asset_id');

        if ($assetIds->isEmpty()) {
            return response()->json([]);
        }

        // Get assets with their relationships
        $assets = Asset::whereIn('id', $assetIds)
            ->with(['assetCategory', 'brand'])
            ->orderBy('asset_id')
            ->get()
            ->map(function($asset) {
                return [
                    'asset_id' => $asset->asset_id ?? 'N/A',
                    'category' => $asset->assetCategory->category_name ?? 'N/A',
                    'brand' => $asset->brand->brand_name ?? 'N/A',
                    'serial_number' => $asset->serial_number ?? 'N/A',
                    'po_number' => $asset->po_number ?? 'N/A',
                    'purchase_date' => $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : 'N/A',
                    'expiry_date' => $asset->expiry_date ? \Carbon\Carbon::parse($asset->expiry_date)->format('Y-m-d') : 'N/A',
                    'status' => $asset->status ?? 'N/A'
                ];
            });

        return response()->json($assets);
    }
}

