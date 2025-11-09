<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Box;
use App\Models\Contact;
use App\Models\Order;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'box_id',
        'sender_id',
        'receiver_id',
        'product_weight',
        'total_weight',
        'carrier',
        'tracking_number',
        'label_path',
        'status',
    ];

    protected $casts = [
        'product_weight' => 'float',
        'total_weight'   => 'float',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function sender()
    {
        return $this->belongsTo(Contact::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Contact::class, 'receiver_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
