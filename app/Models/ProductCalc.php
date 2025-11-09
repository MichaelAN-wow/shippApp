<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Unit;

class ProductCalc extends Model
{
    use HasFactory;
    protected $fillable = ['headers', 'data', 'formulas', 'unit_id', 'company_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
