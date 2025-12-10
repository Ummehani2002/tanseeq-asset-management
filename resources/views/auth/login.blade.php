@extends('layouts.guest')

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center">Login</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100" style="background-color: var(--primary); border-color: var(--primary);">Login</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('register') }}">Create New User</a>
    </div>
</div>
@endsection
