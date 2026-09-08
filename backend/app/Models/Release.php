<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_profile_id',
        'title',
        'slug',
        'type',
        'cover_art_url',
        'release_date',
        'status',
        'streaming_links',
    ];

    protected $casts = [
        'streaming_links' => 'array',
        'release_date' => 'date',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class, 'artist_profile_id');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    public function rightsRecords(): HasMany
    {
        return $this->hasMany(RightsRecord::class);
    }
}
