<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'venue',
        'city',
        'event_date',
        'description',
        'ticket_link',
        'status',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(ArtistProfile::class);
    }
}
