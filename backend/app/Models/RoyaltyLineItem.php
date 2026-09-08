<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoyaltyLineItem extends Model
{
    protected $fillable = ['royalty_statement_id', 'source', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function statement(): BelongsTo
    {
        return $this->belongsTo(RoyaltyStatement::class, 'royalty_statement_id');
    }
}
