<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Material;
use App\Models\Product;

class Unit extends Model
{
    use HasFactory;

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
