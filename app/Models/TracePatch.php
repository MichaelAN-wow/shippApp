<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracePatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'affected_table',
        'affected_id',
        'details',
        'old_value',
        'new_value',
        'company_id',
        'user_id',
    ];
}