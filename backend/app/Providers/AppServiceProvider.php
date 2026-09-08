<?php

namespace App\Providers;

use App\Models\ArtistApplication;
use App\Models\ArtistProfile;
use App\Models\Event;
use App\Models\LicensingRequest;
use App\Models\NewsPost;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Release;
use App\Models\RightsRecord;
use App\Models\RoyaltyStatement;
use App\Models\User;
use App\Observers\AuditLogObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Audit trail (discovery.md §2/§3 Audit Log) — every entity an admin
        // directly creates/edits/deletes through /admin/* routes.
        foreach ([
            ArtistProfile::class,
            ArtistApplication::class,
            Release::class,
            Event::class,
            NewsPost::class,
            Product::class,
            RightsRecord::class,
            RoyaltyStatement::class,
            LicensingRequest::class,
            Partner::class,
            User::class,
        ] as $model) {
            $model::observe(AuditLogObserver::class);
        }
    }
}
