<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'length', 'height', 'width', 'empty_weight',
        'quantity', 'supplier', 'cost'
    ];

    
    public function getWeightLbsOuncesAttribute()
    {
        $lbs = floor($this->empty_weight);
        $oz = round(($this->empty_weight - $lbs) * 16);
        return "{$lbs} lbs {$oz} oz";
    }
}
