<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopifyLocation extends Model
{
    protected $table = 'shopify_location';
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id'
    ];
}
