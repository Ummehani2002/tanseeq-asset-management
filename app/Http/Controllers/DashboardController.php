<?php
namespace App\Http\Controllers;
use App\Models\AssetCategory;

class DashboardController extends Controller
{
 public function index()
{
    $categoryCounts = AssetCategory::withCount('assets')->get();

    return view('dashboard', compact('categoryCounts'));
}
}
