<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_type',
        'shopify_id',
        'shopify_order_name',
        'sale_date',
        'customer_id',
        'discount',
        'tax',
        'shipping',
        'total',
        'fulfillment_status',
        'status',
        'notes',
        'company_id'
    ];
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }
}
