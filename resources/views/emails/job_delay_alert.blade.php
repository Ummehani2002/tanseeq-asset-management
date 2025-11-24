<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Job Delay Alert</title>
</head>
<body>
    <h2>⚠️ Job Delay Alert</h2>
    <p><strong>Employee:</strong> {{ $record->employee_name }}</p>
    <p><strong>Project:</strong> {{ $record->project_name }}</p>
    <p><strong>Ticket Number:</strong> {{ $record->ticket_number }}</p>
    <p><strong>Start Time:</strong> {{ $record->start_time }}</p>
    <p><strong>End Time:</strong> {{ $record->end_time }}</p>
    <p><strong>Standard Hours:</strong> {{ $record->standard_man_hours }}</p>
    <p><strong>Actual Duration:</strong> 
        {{ round($record->end_time->diffInMinutes($record->start_time) / 60, 2) }} hours
    </p>
    <p><strong>Delayed Days:</strong> {{ $record->delayed_days }}</p>
    <p><strong>Performance:</strong> {{ $record->performance_percent }}%</p>

    <p style="color:red;">This job has exceeded its allotted time or deadline.</p>
</body>
</html>
