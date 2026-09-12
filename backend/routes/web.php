<?php

use App\Http\Controllers\Api\MediaUploadController;
use App\Http\Controllers\Web\ApplicationController;
use App\Http\Controllers\Web\ArtistController;
use App\Http\Controllers\Web\AuditLogController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\LicensingRequestController;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\PartnerController;
use App\Http\Controllers\Web\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Web\Portal\EventController as PortalEventController;
use App\Http\Controllers\Web\Portal\NotificationController as PortalNotificationController;
use App\Http\Controllers\Web\Portal\ProfileController as PortalProfileController;
use App\Http\Controllers\Web\Portal\ReleaseController as PortalReleaseController;
use App\Http\Controllers\Web\Portal\RoyaltyStatementController as PortalRoyaltyStatementController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ReleaseController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\RightsRecordController;
use App\Http\Controllers\Web\RoyaltyStatementController;
use App\Http\Controllers\Web\Site\ArtistController as SiteArtistController;
use App\Http\Controllers\Web\Site\ContactController as SiteContactController;
use App\Http\Controllers\Web\Site\EventController as SiteEventController;
use App\Http\Controllers\Web\Site\HomeController as SiteHomeController;
use App\Http\Controllers\Web\Site\LicensingRequestController as SiteLicensingRequestController;
use App\Http\Controllers\Web\Site\NewsController as SiteNewsController;
use App\Http\Controllers\Web\Site\PageController as SitePageController;
use App\Http\Controllers\Web\Site\PartnerController as SitePartnerController;
use App\Http\Controllers\Web\Site\ProductController as SiteProductController;
use App\Http\Controllers\Web\Site\ReleaseController as SiteReleaseController;
use App\Http\Controllers\Web\UserController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

// Blade/session-auth surface (Conversion-README.md) — separate from
// routes/api.php's Sanctum-token API, which is untouched. "web" guard,
// login rate-limited by the same named limiter the API login uses
// (AppServiceProvider::boot()'s "login" RateLimiter, keyed by IP+email).
// Per-module role tiers mirror routes/api.php exactly (same discovery.md
// §3 matrix, same legend: "Manage" excludes destructive delete).
$artistMgmtRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER];
$artistMgmtWrite = [Role::SUPER_ADMIN, Role::AR_MANAGER];
$catalogueRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::CONTENT_MANAGER];
$rightsRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$rightsWrite = [Role::SUPER_ADMIN, Role::AR_MANAGER];
$royaltyRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$royaltyWrite = [Role::SUPER_ADMIN, Role::FINANCE];
$licensingRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$licensingWrite = [Role::SUPER_ADMIN];
$partnersRead = [Role::SUPER_ADMIN, Role::MANAGEMENT];
$partnersWrite = [Role::SUPER_ADMIN];
$eventsRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::CONTENT_MANAGER];
$eventsWrite = [Role::SUPER_ADMIN, Role::AR_MANAGER];
$storeRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::CONTENT_MANAGER];
$storeWrite = [Role::SUPER_ADMIN, Role::CONTENT_MANAGER];
$contentRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::CONTENT_MANAGER];
$contentWrite = [Role::SUPER_ADMIN, Role::CONTENT_MANAGER];
$usersOnly = [Role::SUPER_ADMIN];
$auditLogRead = [Role::SUPER_ADMIN, Role::MANAGEMENT];
$reportsRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$mediaUpload = [Role::SUPER_ADMIN, Role::AR_MANAGER, Role::CONTENT_MANAGER];

// Public marketing site (Conversion-README.md Phase 3) — unauthenticated,
// same public-scoped Eloquent queries as their routes/api.php counterparts
// (Api\ArtistController, Api\ReleaseController, etc.). The four enquiry
// forms share the "public-forms" limiter with routes/api.php's equivalents
// (AppServiceProvider::boot() keys it by IP only, so the bucket is
// genuinely shared, not just same-named).
Route::name('site.')->group(function () {
    Route::get('/', [SiteHomeController::class, 'index'])->name('home');
    Route::get('about', [SitePageController::class, 'about'])->name('about');
    Route::get('videos', [SitePageController::class, 'videos'])->name('videos');

    Route::get('artists', [SiteArtistController::class, 'index'])->name('artists.index');
    Route::get('artists/{slug}', [SiteArtistController::class, 'show'])->name('artists.show');
    Route::post('artists/apply', [SiteArtistController::class, 'storeApplication'])
        ->middleware('throttle:public-forms')->name('artists.apply');

    Route::get('music', [SiteReleaseController::class, 'index'])->name('music.index');
    Route::get('music/{slug}', [SiteReleaseController::class, 'show'])->name('music.show');

    Route::get('events', [SiteEventController::class, 'index'])->name('events.index');
    Route::get('events/{slug}', [SiteEventController::class, 'show'])->name('events.show');

    Route::get('news', [SiteNewsController::class, 'index'])->name('news.index');
    Route::get('news/{slug}', [SiteNewsController::class, 'show'])->name('news.show');

    Route::get('store', [SiteProductController::class, 'index'])->name('store.index');
    Route::get('store/{slug}', [SiteProductController::class, 'show'])->name('store.show');

    Route::get('contact', [SiteContactController::class, 'show'])->name('contact.show');
    Route::post('contact', [SiteContactController::class, 'store'])
        ->middleware('throttle:public-forms')->name('contact.store');

    Route::get('licensing', [SiteLicensingRequestController::class, 'show'])->name('licensing.show');
    Route::post('licensing', [SiteLicensingRequestController::class, 'store'])
        ->middleware('throttle:public-forms')->name('licensing.store');

    Route::get('partnerships', [SitePartnerController::class, 'show'])->name('partnerships.show');
    Route::post('partnerships', [SitePartnerController::class, 'store'])
        ->middleware('throttle:public-forms')->name('partnerships.store');
});

Route::get('admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'adminLogin'])->middleware('throttle:login')->name('admin.login.attempt');

Route::get('portal/login', [AuthController::class, 'showPortalLogin'])->name('portal.login');
Route::post('portal/login', [AuthController::class, 'portalLogin'])->middleware('throttle:login')->name('portal.login.attempt');

Route::middleware('auth')->group(function () use (
    $artistMgmtRead, $artistMgmtWrite, $catalogueRead,
    $rightsRead, $rightsWrite, $royaltyRead, $royaltyWrite, $licensingRead, $licensingWrite,
    $partnersRead, $partnersWrite, $eventsRead, $eventsWrite, $storeRead, $storeWrite,
    $contentRead, $contentWrite, $usersOnly, $auditLogRead, $reportsRead, $mediaUpload
) {
    Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::post('portal/logout', [AuthController::class, 'logout'])->name('portal.logout');

    // Admin dashboard — RBAC per discovery.md §3, same role: middleware
    // used throughout routes/api.php (guard-agnostic — checks $request->user()
    // regardless of which guard authenticated the request).
    Route::middleware('role:'.implode(',', [
        Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE, Role::CONTENT_MANAGER,
    ]))->prefix('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    Route::prefix('admin')->name('admin.')->group(function () use (
        $artistMgmtRead, $artistMgmtWrite, $catalogueRead,
        $rightsRead, $rightsWrite, $royaltyRead, $royaltyWrite, $licensingRead, $licensingWrite,
        $partnersRead, $partnersWrite, $eventsRead, $eventsWrite, $storeRead, $storeWrite,
        $contentRead, $contentWrite, $usersOnly, $auditLogRead, $reportsRead, $mediaUpload
    ) {
        // Artist Management (discovery.md §3) — Full: Super Admin, Manage: A&R, Read: Management.
        Route::middleware('role:'.implode(',', $artistMgmtRead))->group(function () {
            Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
        });
        Route::middleware('role:'.implode(',', $artistMgmtWrite))->group(function () {
            Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
        });

        Route::middleware('role:'.implode(',', $artistMgmtRead))->group(function () {
            Route::get('artists', [ArtistController::class, 'index'])->name('artists.index');
        });
        Route::middleware('role:'.implode(',', $catalogueRead))->group(function () {
            Route::get('releases', [ReleaseController::class, 'index'])->name('releases.index');
        });
        Route::middleware('role:'.implode(',', $artistMgmtWrite))->group(function () {
            Route::get('artists/create', [ArtistController::class, 'create'])->name('artists.create');
            Route::post('artists', [ArtistController::class, 'store'])->name('artists.store');
            Route::get('artists/{artist}/edit', [ArtistController::class, 'edit'])->name('artists.edit');
            Route::patch('artists/{artist}', [ArtistController::class, 'update'])->name('artists.update');

            Route::get('releases/create', [ReleaseController::class, 'create'])->name('releases.create');
            Route::post('releases', [ReleaseController::class, 'store'])->name('releases.store');
            Route::get('releases/{release}/edit', [ReleaseController::class, 'edit'])->name('releases.edit');
            Route::patch('releases/{release}', [ReleaseController::class, 'update'])->name('releases.update');
        });
        Route::middleware('role:'.Role::SUPER_ADMIN)->group(function () {
            Route::delete('artists/{artist}', [ArtistController::class, 'destroy'])->name('artists.destroy');
            Route::delete('releases/{release}', [ReleaseController::class, 'destroy'])->name('releases.destroy');
        });

        // Rights Management (discovery.md §3) — Full: Super Admin, Manage: A&R, Read: Management + Finance.
        Route::middleware('role:'.implode(',', $rightsRead))->group(function () {
            Route::get('rights', [RightsRecordController::class, 'index'])->name('rights.index');
        });
        Route::middleware('role:'.implode(',', $rightsWrite))->group(function () {
            Route::get('rights/create', [RightsRecordController::class, 'create'])->name('rights.create');
            Route::post('rights', [RightsRecordController::class, 'store'])->name('rights.store');
            Route::get('rights/{right}/edit', [RightsRecordController::class, 'edit'])->name('rights.edit');
            Route::patch('rights/{right}', [RightsRecordController::class, 'update'])->name('rights.update');
        });
        Route::middleware('role:'.Role::SUPER_ADMIN)->group(function () {
            Route::delete('rights/{right}', [RightsRecordController::class, 'destroy'])->name('rights.destroy');
        });

        // Royalty Management (discovery.md §3) — Full: Super Admin, Manage: Finance, Read: Management + A&R.
        Route::middleware('role:'.implode(',', $royaltyRead))->group(function () {
            Route::get('royalty', [RoyaltyStatementController::class, 'index'])->name('royalty.index');
        });
        Route::middleware('role:'.implode(',', $royaltyWrite))->group(function () {
            Route::get('royalty/create', [RoyaltyStatementController::class, 'create'])->name('royalty.create');
            Route::post('royalty', [RoyaltyStatementController::class, 'store'])->name('royalty.store');
            Route::get('royalty/{statement}/edit', [RoyaltyStatementController::class, 'edit'])->name('royalty.edit');
            Route::patch('royalty/{statement}', [RoyaltyStatementController::class, 'update'])->name('royalty.update');
        });
        Route::middleware('role:'.Role::SUPER_ADMIN)->group(function () {
            Route::delete('royalty/{statement}', [RoyaltyStatementController::class, 'destroy'])->name('royalty.destroy');
        });

        // Licensing Requests (discovery.md §3) — Full: Super Admin only, Read: Management + A&R + Finance.
        Route::middleware('role:'.implode(',', $licensingRead))->group(function () {
            Route::get('licensing', [LicensingRequestController::class, 'index'])->name('licensing.index');
        });
        Route::middleware('role:'.implode(',', $licensingWrite))->group(function () {
            Route::patch('licensing/{licensingRequest}/status', [LicensingRequestController::class, 'updateStatus'])->name('licensing.status');
            Route::delete('licensing/{licensingRequest}', [LicensingRequestController::class, 'destroy'])->name('licensing.destroy');
        });

        // Partnerships (discovery.md §3) — Full: Super Admin only, Read: Management only.
        Route::middleware('role:'.implode(',', $partnersRead))->group(function () {
            Route::get('partners', [PartnerController::class, 'index'])->name('partners.index');
        });
        Route::middleware('role:'.implode(',', $partnersWrite))->group(function () {
            Route::patch('partners/{partner}/status', [PartnerController::class, 'updateStatus'])->name('partners.status');
            Route::delete('partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');
        });

        // Events & Bookings (discovery.md §3) — Full: Super Admin, Manage: A&R, Read: Management + Content Manager.
        Route::middleware('role:'.implode(',', $eventsRead))->group(function () {
            Route::get('events', [EventController::class, 'index'])->name('events.index');
        });
        Route::middleware('role:'.implode(',', $eventsWrite))->group(function () {
            Route::get('events/create', [EventController::class, 'create'])->name('events.create');
            Route::post('events', [EventController::class, 'store'])->name('events.store');
            Route::get('events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
            Route::patch('events/{event}', [EventController::class, 'update'])->name('events.update');
        });
        Route::middleware('role:'.Role::SUPER_ADMIN)->group(function () {
            Route::delete('events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        });

        // Store (discovery.md §3) — Full: Super Admin, Manage: Content Manager, Read: Management.
        Route::middleware('role:'.implode(',', $storeRead))->group(function () {
            Route::get('store', [ProductController::class, 'index'])->name('store.index');
        });
        Route::middleware('role:'.implode(',', $storeWrite))->group(function () {
            Route::get('store/create', [ProductController::class, 'create'])->name('store.create');
            Route::post('store', [ProductController::class, 'store'])->name('store.store');
            Route::get('store/{product}/edit', [ProductController::class, 'edit'])->name('store.edit');
            Route::patch('store/{product}', [ProductController::class, 'update'])->name('store.update');
        });
        Route::middleware('role:'.Role::SUPER_ADMIN)->group(function () {
            Route::delete('store/{product}', [ProductController::class, 'destroy'])->name('store.destroy');
        });

        // Content Management (discovery.md §3) — Full: Super Admin, Manage: Content Manager, Read: Management.
        Route::middleware('role:'.implode(',', $contentRead))->group(function () {
            Route::get('content', [NewsController::class, 'index'])->name('content.index');
        });
        Route::middleware('role:'.implode(',', $contentWrite))->group(function () {
            Route::get('content/create', [NewsController::class, 'create'])->name('content.create');
            Route::post('content', [NewsController::class, 'store'])->name('content.store');
            Route::get('content/{post}/edit', [NewsController::class, 'edit'])->name('content.edit');
            Route::patch('content/{post}', [NewsController::class, 'update'])->name('content.update');
        });
        Route::middleware('role:'.Role::SUPER_ADMIN)->group(function () {
            Route::delete('content/{post}', [NewsController::class, 'destroy'])->name('content.destroy');
        });

        // Users & Roles (discovery.md §3) — Full: Super Admin only, every
        // other role gets "—". No separate Roles page: the fixed, code-
        // defined set is just queried inline for the Users form's picker.
        Route::middleware('role:'.implode(',', $usersOnly))->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Audit Log (discovery.md §3) — Full: Super Admin, Read: Management.
        // Read-only; entries are written by AuditLogObserver, not this route.
        Route::middleware('role:'.implode(',', $auditLogRead))->group(function () {
            Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
        });

        // Analytics & Reports (discovery.md §3) — Full: Super Admin, Read:
        // Management, Read (own scope): A&R + Finance.
        Route::middleware('role:'.implode(',', $reportsRead))->group(function () {
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        });

        // Shared media upload endpoint (jQuery AJAX) — same controller the
        // Sanctum API route uses; it's guard-agnostic (only reads
        // $request->user()->role), so nothing in it needed changing to be
        // reused here under the session guard instead.
        Route::middleware('role:'.implode(',', $mediaUpload))->group(function () {
            Route::post('uploads', [MediaUploadController::class, 'store'])->name('uploads');
        });
    });

    // Artist Portal (discovery.md §3) — every "Own" cell belongs to the
    // Artist role alone, so a single role gate covers the whole group.
    // Each controller scopes its query to $request->user()->artistProfile
    // rather than trusting any id from the request.
    Route::middleware('role:'.Role::ARTIST)->prefix('portal')->name('portal.')->group(function () {
        Route::get('dashboard', [PortalDashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [PortalProfileController::class, 'show'])->name('profile.show');
        Route::patch('profile', [PortalProfileController::class, 'update'])->name('profile.update');

        Route::get('releases', [PortalReleaseController::class, 'index'])->name('releases.index');
        Route::post('releases', [PortalReleaseController::class, 'store'])->name('releases.store');

        Route::get('royalty-statements', [PortalRoyaltyStatementController::class, 'index'])->name('royalty-statements.index');

        Route::get('bookings', [PortalEventController::class, 'index'])->name('bookings.index');

        Route::get('notifications', [PortalNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/read', [PortalNotificationController::class, 'markRead'])->name('notifications.read');

        Route::post('uploads', [MediaUploadController::class, 'store'])->name('uploads');
    });
});
