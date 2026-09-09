<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoyaltyStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_profile_id',
        'period_start',
        'period_end',
        'total_revenue',
        'company_share',
        'artist_share',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_revenue' => 'decimal:2',
        'company_share' => 'decimal:2',
        'artist_share' => 'decimal:2',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class, 'artist_profile_id');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(RoyaltyLineItem::class);
    }
}
