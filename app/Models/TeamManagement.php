<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamManagement extends Model
{
    protected $table = 'team_time_management';
    protected $fillable = ['user_id', 'date', 'arrival_time', 'departure_time', 'hours', 'notes', 'company_id'];
    use HasFactory;
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
