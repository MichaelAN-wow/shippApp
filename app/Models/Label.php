<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'package',
    ];

    
    public function sender()
    {
        return $this->belongsTo(Contact::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Contact::class, 'recipient_id');
    }
}
