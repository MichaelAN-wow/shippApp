<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StickyNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'folder',
        'tags',
        'content',
        'color',
        'x',
        'y',
        'linked_to',
        'author',
        'trashed',
    ];

    protected $casts = [
        'tags' => 'array',
        'trashed' => 'boolean',
    ];

    // Only return notes that are not trashed unless explicitly filtered
    protected static function booted()
    {
        static::addGlobalScope('notTrashed', function ($query) {
            $query->where('trashed', false);
        });
    }
}
