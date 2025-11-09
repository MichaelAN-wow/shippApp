<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_completed',
        'shopify_location_id',
        'user_id',
        'company_id'
    ];

    public function location()
    {
        return $this->belongsTo('App\Models\ShopifyLocation', 'shopify_location_id', 'id');
    }

    public function production_product()
    {
    // Make sure 'Category' is the correct name of your category model
        return $this->hasMany('App\Models\ProductionProduct', 'production_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
