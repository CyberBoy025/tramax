<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtistProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artist_name',
        'slug',
        'biography',
        'genre',
        'photo_url',
        'social_links',
        'status',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    public function royaltyStatements(): HasMany
    {
        return $this->hasMany(RoyaltyStatement::class);
    }
}
