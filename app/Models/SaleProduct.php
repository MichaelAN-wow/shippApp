<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProduct  extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'unit_price',
        'quantity',
        'subtotal',
        'shopify_id',
        'shopify_variants_id',
        'shopify_product_id',
        'product_name',
        'company_id'
    ];
}
