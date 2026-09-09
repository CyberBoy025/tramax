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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        // README.md §4/§13 — the public unauthenticated POST endpoints
        // (contact, applications, licensing-requests, partners) had no
        // rate limiting at all: open to scripted spam. 5/minute per IP is
        // generous for a human filling out a real form, tight for a bot.
        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Login attempts — keyed by IP *and* the submitted email, so a
        // distributed brute-force against one account and a single IP
        // hammering many accounts both get throttled, not just one.
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip().'|'.(string) $request->input('email'));
        });
    }
}
