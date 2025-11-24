@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Manage Categories</h2>

    {{-- Add New Category Form --}}
    <form action="{{ route('categories.store') }}" method="POST" class="mb-4">
        @csrf
        <input type="text" name="category_name" class="form-control mb-2" placeholder="Add New Category" required>
        <button type="submit" class="btn btn-primary">Add Category</button>
    </form>

    @foreach($categories as $category)
        <h3>{{ $category->category_name }}</h3>

        {{-- Add Brand Form --}}
        <form action="{{ route('brands.store') }}" method="POST" class="mb-3">
            @csrf
            <input type="hidden" name="asset_category_id" value="{{ $category->id }}">
            <input type="text" name="name" class="form-control mb-2" placeholder="Add Brand for {{ $category->category_name }}" required>
            <button type="submit" class="btn btn-success">Add Brand</button>
        </form>

        {{-- Brands & Features Table --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Brand</th>
                    <th>Features</th>
                    <th>Add Feature</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category->brands as $brand)
                    <tr>
                        <td>{{ $brand->name }}</td>
                        <td>
                            @if($brand->features->count())
                                {{ $brand->features->pluck('feature_name')->join(', ') }}
                            @else
                                <em>No features added</em>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('features.store') }}" method="POST" class="d-flex">
                                @csrf
                                <input type="hidden" name="brand_id" value="{{ $brand->id }}">
                                <input type="text" name="feature_name" class="form-control form-control-sm me-2" placeholder="New Feature" required>
                                <button type="submit" class="btn btn-primary btn-sm">Add</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
