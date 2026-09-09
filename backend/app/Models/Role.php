<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// The seven roles from discovery.md §3 — Super Administrator, Management,
// A&R / Artist Manager, Finance, Content Manager, Artist, Partner/External User.
class Role extends Model
{
    public const SUPER_ADMIN = 'Super Administrator';

    public const MANAGEMENT = 'Management';

    public const AR_MANAGER = 'A&R / Artist Manager';

    public const FINANCE = 'Finance';

    public const CONTENT_MANAGER = 'Content Manager';

    public const ARTIST = 'Artist';

    public const PARTNER = 'Partner/External User';

    public const ALL = [
        self::SUPER_ADMIN,
        self::MANAGEMENT,
        self::AR_MANAGER,
        self::FINANCE,
        self::CONTENT_MANAGER,
        self::ARTIST,
        self::PARTNER,
    ];

    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
