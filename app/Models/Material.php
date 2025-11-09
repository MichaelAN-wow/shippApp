<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = [
        'name',
        'material_type',
        'current_stock_level',
        'min_stock_level',
        'price_per_unit',
        'material_code',
        'notes',
        'unit_id',
        'photo_path',
        'supplier_id',
        'category_id',
        'last_order_date',
        'company_id',
        'material_base',
        'total_weight',
        ];
    use HasFactory;

    public function unit()
    {
    // Make sure 'Supplier' is the correct name of your supplier model
        return $this->belongsTo('App\Models\Unit', 'unit_id', 'id');
    }

    public function supplier()
    {
    // Make sure 'Supplier' is the correct name of your supplier model
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    public function category()
    {
    // Make sure 'Category' is the correct name of your category model
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function materialTransactions()
    {
        return $this->hasMany('App\Models\MaterialTransaction');
    }

    public static function updateMaterialInfo($materialId, $newPrice, $newStockLevel)
    {
        $material = self::find($materialId);
        if ($material) {
            $material->price_per_unit = $newPrice;
            $material->current_stock_level = $material->current_stock_level + $newStockLevel;
            $material->save();

            return $material->current_stock_level;
        }
        return null;
    }
}
