<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
