@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Complete Job Card</h2>

    <form action="{{ route('time.update', $record->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Ticket Number</label>
            <input type="text" class="form-control" value="{{ $record->ticket_number }}" readonly>
        </div>

        <div class="mb-3">
            <label>Start Time</label>
            <input type="text" class="form-control" value="{{ $record->start_time }}" readonly>
        </div>

        <div class="mb-3">
            <label>End Time</label>
            <input type="datetime-local" name="end_time" class="form-control" 
                   value="{{ $record->end_time ? $record->end_time->format('Y-m-d\TH:i') : '' }}">
        </div>

        <div class="mb-3">
            <label>Delay Reason (if any)</label>
            <textarea name="delay_reason" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Mark as Completed</button>
        <a href="{{ route('time.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
