@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dashboard</h2>

    <h4>Asset Categories</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Assets</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categoryCounts as $category)
                <tr>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->assets_count }}</td>
                    <td>
                        <a href="{{ route('assets.byCategory', $category->id) }}" class="btn btn-sm btn-outline-primary">
                            View Assets
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection





