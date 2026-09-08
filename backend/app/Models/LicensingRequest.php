<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicensingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'music_required',
        'project_type',
        'usage',
        'duration',
        'territory',
        'budget',
        'message',
        'related_release_id',
        'status',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'related_release_id');
    }
}
