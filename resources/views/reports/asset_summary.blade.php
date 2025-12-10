@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Asset Summary Report (Entity-wise)</h2>

    @foreach($assets as $entity => $entityAssets)
        <h4>{{ $entity }}</h4>
        <table class="table table-bordered table-striped mb-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Asset ID</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Serial Number</th>
                    <th>Purchase Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entityAssets as $asset)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $asset->asset_id }}</td>
                        <td>{{ $asset->category->category_name ?? '-' }}</td>
                        <td>{{ $asset->brand->name ?? '-' }}</td>
                        <td>{{ $asset->serial_number }}</td>
                        <td>{{ $asset->purchase_date }}</td>
                        <td>{{ $asset->expiry_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
