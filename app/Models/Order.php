<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;


use App\Models\OrderMaterial;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'status',
        'received_at',
        'discount',
        'tax',
        'shipping',
        'total',
        'order_number',
        'notes',
        'company_id'
    ];

    public function supplier()
    {
    // Make sure 'Category' is the correct name of your category model
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    public function orderMaterials()
    {
        return $this->hasMany(OrderMaterial::class);
    }
    public function getReadableStatusAttribute()
    {
        return match ((int) $this->status) {
            2 => 'Pending',
            1 => 'In Transit',
            0 => 'Shipped',
            default => 'Unknown',
        };
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'order_id');
    }

}
