<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTransaction extends Model
{
    protected $table = 'product_transactions';

    protected $fillable = [
        'product_id',
        'user_id',
        'sale_id',
        'production_id',
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
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
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
        return $this->belongsTo('App\Models\Sale', 'sale_id', 'id');
    }

    public function product()
    {
    // Make sure 'Supplier' is the correct name of your supplier model
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function production()
    {
    // Make sure 'Supplier' is the correct name of your supplier model
        return $this->belongsTo('App\Models\Production', 'production_id', 'id');
    }
}
