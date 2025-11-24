<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Mail\AssetAssigned;



// Guest Pages
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // All your other routes for assets, users, brands, etc.
});


use App\Http\Controllers\UserController;
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');




use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

use App\Http\Controllers\AssetCategoryController;
Route::resource('categories', AssetCategoryController::class);
Route::get('/manage-categories', [AssetCategoryController::class, 'index'])->name('categories.manage');
Route::post('/categories/store', [AssetCategoryController::class, 'storeCategory'])->name('categories.store');

use App\Http\Controllers\BrandController;
Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
Route::post('/brands/store', [AssetCategoryController::class, 'storeBrand'])->name('brands.store');
Route::get('/brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
Route::put('/brands/{id}', [BrandController::class, 'update'])->name('brands.update');
Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');


use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetController;

Route::get('/employees/autocomplete', [EmployeeController::class, 'autocomplete'])->name('employees.autocomplete');
Route::get('/employees/{id}/assets', [AssetController::class, 'getAssetsByEmployee'])->name('employees.assets');
Route::get('/employees/{id}/assets', [AssetController::class, 'getAssetsByEmployee'])->name('employees.assets');

Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
Route::get('/features/by-brand/{id}', [AssetController::class, 'getFeatures']);


use App\Http\Controllers\CategoryFeatureController;

Route::post('/features/store', [AssetCategoryController::class, 'storeFeature'])->name('features.store');
Route::get('/brands/by-category/{categoryId}', [BrandController::class, 'getByCategory']);
Route::get('/assets/category/{id}', [AssetController::class, 'assetsByCategory'])->name('assets.byCategory');
Route::get('/category-features/{category}', [CategoryFeatureController::class, 'getByCategory']);
Route::post('/features/store', [AssetCategoryController::class, 'storeFeature'])->name('features.store');
Route::get('/features/by-brand/{brandId}', [CategoryFeatureController::class, 'getByBrand']);
Route::get('/features/by-brand/{id}', [AssetController::class, 'getFeaturesByBrand']);
Route::get('/features/{id}/edit', [CategoryFeatureController::class, 'edit'])->name('features.edit');
Route::put('/features/{id}', [CategoryFeatureController::class, 'update'])->name('features.update');
Route::delete('/features/{id}', [CategoryFeatureController::class, 'destroy'])->name('features.destroy');



Route::get('/employee-master', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employee-master/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employee-master', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/employee-master/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::delete('/employee-master/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::get('/employees/autocomplete', [EmployeeController::class, 'autocomplete'])->name('employees.autocomplete');
Route::put('/employee-master/{employee}', [EmployeeController::class, 'update'])->name('employees.update');


Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');



use App\Http\Controllers\LocationController;
Route::get('/location-master', [LocationController::class, 'index'])->name('location-master.index');
Route::post('/location-master', [LocationController::class, 'store'])->name('location-master.store');
Route::post('/location-master', [LocationController::class, 'store'])->name('location-master.store'); // ✅ For CREATE
Route::put('/location-master/{id}', [LocationController::class, 'update'])->name('location-master.update'); // ✅ For UPDATE
Route::delete('/location-master/{id}', [LocationController::class, 'destroy'])->name('location-master.destroy');

Route::get('/location-autocomplete', [App\Http\Controllers\LocationController::class, 'autocomplete'])->name('location.autocomplete');


// ...existing code...
// Autocomplete
Route::get('/locations-autocomplete', [LocationController::class, 'autocomplete'])
    ->name('locations.autocomplete');

// Get assets of selected location
Route::get('/locations/{id}/assets', [LocationController::class, 'assets']);




use App\Http\Controllers\EmployeeAssetController;
Route::get('/employee-assets', [EmployeeAssetController::class, 'index'])->name('employee.assets');
Route::get('/employee/search', [App\Http\Controllers\EmployeeController::class, 'search'])->name('employee.search');



use App\Http\Controllers\LocationAssetController;
Route::get('/location-assets', [LocationAssetController::class, 'index'])->name('location.assets');
Route::get('/locations/autocomplete', [LocationAssetController::class, 'autocomplete'])->name('locations.autocomplete');
Route::get('/location-master/{id}/edit', [LocationController::class, 'edit'])->name('location.edit');
Route::put('/location-master/{id}', [LocationController::class, 'update'])->name('location.update');
Route::delete('/location-master/{id}', [LocationController::class, 'destroy'])->name('location.destroy');



use App\Http\Controllers\AssetTransactionController;
Route::get('/get-assets-by-category/{id}', [AssetTransactionController::class, 'getAssetsByCategory']);
Route::get('/get-category-name/{id}', [AssetTransactionController::class, 'getCategoryName']);
Route::get('/asset-transactions/create', [AssetTransactionController::class, 'create'])->name('asset-transactions.create');
Route::post('/asset-transactions/store', [AssetTransactionController::class, 'store'])->name('asset-transactions.store');
Route::get('/asset-transactions', [AssetTransactionController::class, 'index'])->name('asset-transactions.index');
Route::get('/asset-transactions/filter', [AssetTransactionController::class, 'filter'])->name('asset-transactions.filter');

Route::get('/asset-transactions/{id}/edit', [AssetTransactionController::class, 'edit'])->name('asset-transactions.edit');
Route::put('/asset-transactions/{id}', [AssetTransactionController::class, 'update'])->name('asset-transactions.update');
Route::get('/maintenance/form', [AssetTransactionController::class, 'systemMaintenanceForm'])->name('maintenance.form');
Route::get('/maintenance/fetch', [AssetTransactionController::class, 'fetchEmployeeAssets'])->name('maintenance.fetch');
Route::post('/maintenance/save', [AssetTransactionController::class, 'saveSystemMaintenance'])->name('maintenance.save');
Route::get('/assets/filter', [App\Http\Controllers\AssetController::class, 'filter'])->name('assets.filter');
Route::get('/get-asset-full-details/{asset_id}', [AssetController::class, 'getFullDetails']);

Route::get('/system-maintenance/form', [AssetTransactionController::class, 'showMaintenanceForm'])->name('system.maintenance.form');
Route::post('/system-maintenance/save', [AssetTransactionController::class, 'saveMaintenance'])->name('system.maintenance.save');
Route::get('/get-assets-by-category/{id}', [AssetTransactionController::class, 'getAssets']);
Route::get('/get-category-name/{id}', [AssetTransactionController::class, 'getCategoryName']);
Route::get('/get-asset-details/{id}', [AssetTransactionController::class, 'getAssetDetails']);


use App\Http\Controllers\AssetHistoryController;
Route::get('/asset-history/{asset_id}', [AssetHistoryController::class, 'show'])->name('asset.history');
Route::get('/categories/{id}/edit', [AssetCategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/{id}', [AssetCategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{id}', [AssetCategoryController::class, 'destroy'])->name('categories.destroy');
Route::get('/test-email', function () {
    $txn = \App\Models\AssetTransaction::with('employee', 'asset.category')->latest()->first();
     if ($txn && $txn->employee && $txn->employee->email) {
        Mail::to($txn->employee->email)->send(new AssetAssigned($txn));
        return "Email sent!";
    }
     return "No transaction found with employee email.";
});


Route::get('/api/assigned-assets/{employee_id}', function ($employee_id) {
    $assignedAssets = \App\Models\AssetTransaction::with('asset.assetCategory')
        ->where('employee_id', $employee_id)
        ->where('transaction_type', 'assign')
        ->get()
        ->map(function ($t) {
            return [
                'id' => $t->asset->id,
                'asset_id' => $t->asset->asset_id,
                'asset_category' => $t->asset->assetCategory->category_name ?? 'N/A'];
        });
    return response()->json($assignedAssets);
});



use App\Http\Controllers\EntityBudgetController;
Route::get('/entity-budget/create', [EntityBudgetController::class, 'create'])->name('entity_budget.create');
Route::post('/entity-budget/store', [EntityBudgetController::class, 'store'])->name('entity_budget.store');




use App\Http\Controllers\BudgetExpenseController;
Route::get('/budget-expenses/create', [BudgetExpenseController::class, 'create'])->name('budget-expenses.create');
Route::post('/budget-expenses/store', [BudgetExpenseController::class, 'store'])->name('budget-expenses.store');
Route::get('/budget-expenses/get-details', [BudgetExpenseController::class, 'getBudgetDetails'])->name('budget-expenses.get-details');


use App\Http\Controllers\TimeManagementController;

Route::get('/time-management', [TimeManagementController::class, 'index'])->name('time.index');
Route::get('/time-management/create', [TimeManagementController::class, 'create'])->name('time.create');
Route::post('/time-management/store', [TimeManagementController::class, 'store'])->name('time.store');
Route::get('/time-management/{id}/edit', [TimeManagementController::class, 'edit'])->name('time.edit');
Route::post('/time-management/{id}/update', [TimeManagementController::class, 'update'])->name('time.update');
Route::delete('/time-management/{id}', [TimeManagementController::class, 'destroy'])->name('time.destroy');


use App\Http\Controllers\IssueNoteController;

Route::get('/issue-note/create', [IssueNoteController::class, 'create'])->name('issue-note.create');
Route::post('/issue-note/store', [IssueNoteController::class, 'store'])->name('issue-note.store');
Route::get('/employee/details/{id}', [EmployeeController::class, 'getDetails']); 
