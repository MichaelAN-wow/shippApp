<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TossedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'quantity',
        'reason',
        'company_id',
        'user_id',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scope to filter by company
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}