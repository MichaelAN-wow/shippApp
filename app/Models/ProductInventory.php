<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    use HasFactory;

    protected $table = 'product_inventory';
    protected $fillable = [
        'product_id',
        'location_id',
        'current_stock_level',
        'min_stock_level',
        'company_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(ShopifyLocation::class, 'location_id');
    }
}
