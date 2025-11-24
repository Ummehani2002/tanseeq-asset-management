<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Imports\LocationsImport;
use App\Models\Asset;

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
    $query = $request->get('query');

    $locations = \App\Models\Location::where('location_name', 'LIKE', $query.'%')
        ->orWhere('location_id', 'LIKE', $query.'%')
        ->limit(10)
        ->get(['location_id', 'location_name']);

    return response()->json($locations);
}


    // GET /locations/{id}/assets
    public function assets($id)
    {
        $assets = Asset::where('location_id', $id)
            ->orderBy('asset_id')
            ->get([
                'asset_id',
                'category',
                'brand',
                'serial_number',
                'po_number',
                'purchase_date',
                'expiry_date'
            ]);

        return response()->json($assets);
    }
}

