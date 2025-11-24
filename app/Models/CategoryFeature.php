<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CategoryFeature extends Model
{
    protected $fillable = [
        'asset_category_id',
        'brand_id',
        'feature_name',
    ];

    
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
