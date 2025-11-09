<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Material;
class ProductMaterial  extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'material_id',
        'unit_id',
        'unit_price',
        'used_amount',
        'company_id'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
    
}
