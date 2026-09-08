<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_id',
        'title',
        'duration_seconds',
        'isrc',
        'track_number',
        'credits',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }
}
