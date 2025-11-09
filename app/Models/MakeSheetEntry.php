<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeSheetEntry extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'data'];
    
    protected $casts = [
        'data' => 'array'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}