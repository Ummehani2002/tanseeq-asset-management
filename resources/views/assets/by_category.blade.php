@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Assets in Category: {{ $category->category_name }}</h2>

    @if($assets->isEmpty())
        <p>No assets found in this category.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Brand</th>
                    <th>Purchase Date</th>
                    <th>PO Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                    <tr>
                        <td>{{ $asset->asset_id }}</td>
                        <td>{{ $asset->brand->name ?? 'N/A' }}</td>
                        <td>{{ $asset->purchase_date }}</td>
                        <td>{{ $asset->po_number }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
