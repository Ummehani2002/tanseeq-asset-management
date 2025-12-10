<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNote extends Model
{
    use HasFactory;

  protected $fillable = [
    'department',
    'location',
    'system_code',
    'printer_code',
    'software_installed',
    'issued_date',
    'items',
    'user_signature',
    'manager_signature',
];

    protected $casts = [
        'items' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
