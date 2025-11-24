@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Assets List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<form method="GET" action="{{ route('assets.filter') }}" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <label for="category_id" class="form-label">Filter by Category:</label>
            <select name="category_id" id="category_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- All Categories --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (isset($categoryId) && $categoryId == $category->id) ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>

    @if($assets->isEmpty())
        <p>No assets found.</p>
    @else
        <table class="table table-bordered">
    <thead>
        <tr>
            <th>Asset ID</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Purchase Date</th>
            <th>Warranty Start</th>
            <th>Expiry Date</th>
            <th>PO Number</th>
            <th>Serial Number</th>

            <th>Features</th>
            <th>Invoice</th>
        </tr>
    </thead>
    <tbody>
        @forelse($assets as $asset)
        <tr>
            <td>
                {{ $asset->asset_id }} <br>
                <a href="{{ route('asset.history', $asset->id) }}" class="btn btn-sm btn-outline-info mt-1">View History</a>
            </td>
            <td>{{ optional($asset->category)->category_name ?? 'Unknown Category' }}</td>
            <td>{{ optional($asset->brand)->name ?? 'Unknown Brand' }}</td>
            <td>{{ $asset->purchase_date }}</td>
            <td>{{ $asset->warranty_start ?? 'N/A' }}</td>
            <td>{{ $asset->expiry_date ?? 'N/A' }}</td>
            <td>{{ $asset->po_number }}</td>
          <td>{{ $asset->serial_number ?? 'N/A' }}</td>



            <td>
                <ul class="mb-0">
                    @foreach($asset->featureValues as $fv)
                        <li><strong>{{ $fv->feature->feature_name ?? 'N/A' }}</strong>: {{ $fv->value }}</li>
                    @endforeach
                </ul>
            </td>

            <td>
                @if($asset->invoice_path)
                    <a href="{{ asset('storage/' . $asset->invoice_path) }}" target="_blank">View Invoice</a>
                @else
                    N/A
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">No assets found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

    @endif
</div>
@endsection
