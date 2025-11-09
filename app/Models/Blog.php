<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'published_on', 
        'author_name', 
        'author_job', 
        'author_image_url', 
        'content', 
        'status', 
        'content_image_url'
    ];

    protected $casts = [
        'published_on' => 'date',
    ];
}
