<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'department',
        'location',
        'system_code',
        'printer_code',
        'software_installed',
        'issued_date',
        'items',
    ];

    protected $casts = [
        'items' => 'array', // store checkbox items as JSON
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
