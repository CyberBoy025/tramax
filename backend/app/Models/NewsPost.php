<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'cover_image',
        'author_id',
        'published_at',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
