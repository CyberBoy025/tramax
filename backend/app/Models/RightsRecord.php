<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RightsRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_id',
        'track_id',
        'master_owner',
        'publishing_owner',
        'songwriter',
        'producer',
        'copyright_status',
        'licensing_status',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }
}
