<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\Brand;
use App\Models\CategoryFeature;
use App\Models\Asset;
class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::with(['brands.features'])->get();
        $assets = Asset::with(['latestTransaction.location', 'assetCategory'])->get();
        return view('categories.manage', compact('categories', 'assets'));
    }
    public function storeCategory(Request $request)
    {
        $request->validate(['category_name' => 'required|string|unique:asset_categories,category_name']);
        AssetCategory::create($request->only('category_name'));
        return redirect()->back()->with('success', 'Category added successfully!');
    }

    public function storeBrand(Request $request)
    {
        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name' => 'required|string'
        ]);

        Brand::create($request->only('asset_category_id', 'name'));
        return redirect()->back()->with('success', 'Brand added successfully!');
    }

public function storeFeature(Request $request)
{
    $request->validate([
        'brand_id' => 'required|exists:brands,id',
        'feature_name' => 'required|string|max:255',
    ]);

    \App\Models\CategoryFeature::create([
        'brand_id' => $request->brand_id,
        'feature_name' => $request->feature_name,
        'asset_category_id' => $request->brand_id ? Brand::find($request->brand_id)->asset_category_id : null,
    ]);

    return back()->with('success', 'Feature added successfully.');
}
public function edit($id)
{
    $category = AssetCategory::findOrFail($id);
    return view('categories.edit', compact('category'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'category_name' => 'required|string|max:255',
    ]);

    $category = AssetCategory::findOrFail($id);
    $category->update([
        'category_name' => $request->category_name,
    ]);

    return redirect()->route('categories.manage')->with('success', 'Category updated successfully.');
}

public function destroy($id)
{
    $category = AssetCategory::findOrFail($id);

    // Get all brands under this category
    $brands = Brand::where('asset_category_id', $category->id)->get();

    // Delete category features linked to these brands
    foreach ($brands as $brand) {
        \App\Models\CategoryFeature::where('brand_id', $brand->id)->delete();
    }

    // Delete brands
    Brand::where('asset_category_id', $category->id)->delete();

    // Delete category
    $category->delete();

    return redirect()->route('categories.index')->with('success', 'Category and all related data deleted successfully.');
}

public function manageCategories()
{
    $categories = AssetCategory::with(['brands.features'])->get(); // Make sure relationships are defined
    return view('categories.manage', compact('categories'));
}
}
