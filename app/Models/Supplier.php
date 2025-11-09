<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Material;
use App\Models\Order;

class Supplier extends Model
{

    use HasFactory;

    protected $fillable = [
        'name', 'website', 'contact_name', 'phone', 'email', 'notes', 'company_id'
    ];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
