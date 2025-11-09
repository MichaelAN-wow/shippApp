<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialTransaction extends Model
{
    protected $table = 'material_transactions';

    protected $fillable = [
        'material_id',
        'user_id',
        'supplier_id',
        'transaction_type',
        'quantity',
        'before_level',
        'to_level',
        'company_id'
        ];

    /**
    * Get the material that owns the transaction.
    */
    public function material(): BelongsTo
    {
        return $this->belongsTo('App\Models\Material', 'material_id', 'id');
    }

    /**
    * Get the user that made the transaction.
    */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function supplier()
    {
    // Make sure 'Supplier' is the correct name of your supplier model
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }
}
