@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Location Master</title>
</head>
<body>
    <h1>Location Master</h1>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <ul style="color:red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Add New Location</h2>
    <form method="POST" action="{{ route('location-master.store') }}">
        @csrf
        <label>Location ID:</label><br>
        <input type="text" name="location_id" required><br><br>

        <label>Location category:</label><br>
        <input type="text" name="location_category"><br><br>

        <label>Location Name:</label><br>
        <input type="text" name="location_name"><br><br>

        <label>Location Entity:</label><br>
        <input type="text" name="location_entity" required><br><br>

        <button type="submit">Add Location</button>
    </form>

    <h2>Location List</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Location ID</th>
                <th>category</th>
                <th> location Name</th>
                <th>Entity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $loc)
                <tr>
                    <td>{{ $loc->location_id }}</td>
                    <td>{{ $loc->location_category }}</td>
                    <td>{{ $loc->location_name }}</td>
                    <td>{{ $loc->location_entity }}</td>
                 <td>
    <a href="{{ route('location.edit', $loc->id) }}" class="btn btn-warning btn-sm">Edit</a>

    <form action="{{ route('location.destroy', $loc->id) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
    </form>
</td>

                </tr>
            @empty
                <tr><td colspan="4">No locations found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><a href="{{ route('dashboard') }}">Back to Select Master</a></p>
</body>
</html>
@endsection