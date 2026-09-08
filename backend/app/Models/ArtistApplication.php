<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'artist_name',
        'phone',
        'email',
        'location',
        'genre',
        'years_active',
        'social_links',
        'streaming_links',
        'biography',
        'demo_file_url',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'streaming_links' => 'array',
        'submitted_at' => 'datetime',
    ];
}
