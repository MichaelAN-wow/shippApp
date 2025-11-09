<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\Material;
use App\Models\Order;
class OrderMaterial  extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'material_id',
        'unit_id',
        'unit_price',
        'quantity',
        'subtotal',
        'company_id'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    
}
