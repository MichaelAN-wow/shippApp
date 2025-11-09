<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;

class ProductionProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'production_id',
        'product_id',
        'quantity',
        'company_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
