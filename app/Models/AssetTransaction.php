<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    protected $fillable = [
        'transaction_type',
        'employee_id',
        'asset_id',
        'location_id',
        'remarks',
        'issue_date',
        'receive_date',
        'received_by',
        'repair_cost',
        'repair_vendor',
        'repair_type',
        'image_path',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
