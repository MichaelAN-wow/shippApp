<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingConnection extends Model
{
    protected $fillable = [
        'carrier', 'account_number', 'api_key', 'api_secret', 'sandbox'
    ];

    protected $casts = [
        'sandbox' => 'boolean',
    ];
}
