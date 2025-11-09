<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shopify_id',
        'variants_id',
        'name',
        'product_type',
        'current_stock_level',
        'min_stock_level',
        'price',
        'notes',
        'product_code',
        'photo_path',
        'unit_id',
        'supplier_id',
        'category_id',
        'variants_name',
        'last_order_date',
        'company_id',
        'isMaterialBase',
        ];
        
    use HasFactory;

    public function unit()
    {
    // Make sure 'Supplier' is the correct name of your supplier model
        return $this->belongsTo('App\Models\Unit', 'unit_id', 'id');
    }

    public function category()
    {
    // Make sure 'Category' is the correct name of your category model
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function productTransactions()
    {
        return $this->hasMany('App\Models\ProductTransaction');
    }

    public function productMaterials()
    {
        return $this->hasMany('App\Models\ProductMaterial');
    }

    public function productInventory()
    {
        return $this->hasMany('App\Models\ProductInventory');
    }

}
