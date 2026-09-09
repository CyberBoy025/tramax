<?php

use App\Http\Controllers\Api\Admin\ApplicationAdminController;
use App\Http\Controllers\Api\Admin\AuditLogAdminController;
use App\Http\Controllers\Api\Admin\ArtistAdminController;
use App\Http\Controllers\Api\Admin\EventAdminController;
use App\Http\Controllers\Api\Admin\LicensingRequestAdminController;
use App\Http\Controllers\Api\Admin\NewsAdminController;
use App\Http\Controllers\Api\Admin\PartnerAdminController;
use App\Http\Controllers\Api\Admin\ProductAdminController;
use App\Http\Controllers\Api\Admin\ReleaseAdminController;
use App\Http\Controllers\Api\Admin\ReportsAdminController;
use App\Http\Controllers\Api\Admin\RightsRecordAdminController;
use App\Http\Controllers\Api\Admin\RoleAdminController;
use App\Http\Controllers\Api\Admin\RoyaltyStatementAdminController;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\LicensingRequestController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReleaseController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

// Named per-module role tiers, straight off the discovery.md §3 matrix —
// each module's row has its own read set and write set, so a single
// blanket admin gate would either over- or under-grant access.
$artistMgmtRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER];
$artistMgmtWrite = [Role::SUPER_ADMIN, Role::AR_MANAGER];
$rightsRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$rightsWrite = [Role::SUPER_ADMIN, Role::AR_MANAGER];
$royaltyRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$royaltyWrite = [Role::SUPER_ADMIN, Role::FINANCE];
// Licensing: no role has "Manage", only Super Admin has "Full" — so write is
// Super-Admin-only, unlike Rights/Royalty where a second role could write.
$licensingRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];
$licensingWrite = [Role::SUPER_ADMIN];
// Partnerships: the narrowest module built so far — only Management gets Read.
$partnersRead = [Role::SUPER_ADMIN, Role::MANAGEMENT];
$partnersWrite = [Role::SUPER_ADMIN];
// Events & Bookings: Full: Super Admin, Manage: A&R, Read: Management + Content Manager.
$eventsRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::CONTENT_MANAGER];
$eventsWrite = [Role::SUPER_ADMIN, Role::AR_MANAGER];
// Store & Content Management: Full: Super Admin, Manage: Content Manager, Read: Management.
$storeRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::CONTENT_MANAGER];
$storeWrite = [Role::SUPER_ADMIN, Role::CONTENT_MANAGER];
$contentRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::CONTENT_MANAGER];
$contentWrite = [Role::SUPER_ADMIN, Role::CONTENT_MANAGER];
// Users & Roles: Full: Super Admin only, every other role gets "—" — a
// single tier covers both read and write since there's no partial access.
$usersOnly = [Role::SUPER_ADMIN];
// Audit Log: Full: Super Admin, Read: Management — read-only for both, so a
// single tier covers it (no write endpoints exist; see AuditLogObserver).
$auditLogRead = [Role::SUPER_ADMIN, Role::MANAGEMENT];
// Analytics & Reports: Full: Super Admin, Read: Management, Read (own
// scope): A&R + Finance — the route only gates who's allowed in at all;
// ReportsAdminController::summary() branches by role to scope the data.
$reportsRead = [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE];

Route::prefix('v1')->group(function () use (
    $artistMgmtRead, $artistMgmtWrite, $rightsRead, $rightsWrite, $royaltyRead, $royaltyWrite,
    $licensingRead, $licensingWrite, $partnersRead, $partnersWrite, $eventsRead, $eventsWrite,
    $storeRead, $storeWrite, $contentRead, $contentWrite, $usersOnly, $auditLogRead, $reportsRead
) {
    // Public-site endpoints per discovery.md §4.1 — no auth required.
    Route::get('artists', [ArtistController::class, 'index']);
    Route::get('artists/{slug}', [ArtistController::class, 'show']);

    Route::get('releases', [ReleaseController::class, 'index']);
    Route::get('releases/{slug}', [ReleaseController::class, 'show']);

    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{slug}', [EventController::class, 'show']);

    Route::get('news', [NewsController::class, 'index']);
    Route::get('news/{slug}', [NewsController::class, 'show']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);

    Route::post('applications', [ApplicationController::class, 'store']);
    Route::post('licensing-requests', [LicensingRequestController::class, 'store']);
    Route::post('partners', [PartnerController::class, 'store']);
    Route::post('contact', [ContactController::class, 'store']);

    // Auth — shared login for every role (discovery.md §3); the issued
    // token's user->role determines what it can subsequently reach.
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
    });

    // Admin platform — gated per discovery.md §3's RBAC matrix, module by
    // module. Audit Log admin endpoints are still a follow-up.
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () use (
        $artistMgmtRead, $artistMgmtWrite, $rightsRead, $rightsWrite, $royaltyRead, $royaltyWrite,
        $licensingRead, $licensingWrite, $partnersRead, $partnersWrite, $eventsRead, $eventsWrite,
        $storeRead, $storeWrite, $contentRead, $contentWrite, $usersOnly, $auditLogRead, $reportsRead
    ) {
        // Artist Management (discovery.md §3) — Full: Super Admin, Manage: A&R, Read: Management.
        Route::middleware('role:'.implode(',', $artistMgmtRead))->group(function () {
            Route::get('applications', [ApplicationAdminController::class, 'index']);
            Route::get('applications/{application}', [ApplicationAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $artistMgmtWrite))->group(function () {
            Route::patch('applications/{application}/status', [ApplicationAdminController::class, 'updateStatus']);
        });

        // Music Catalogue (discovery.md §3). Admin index/show return every
        // status (not just Published/non-Inactive, unlike the public
        // GET /artists and /releases above) — an admin managing the
        // catalogue needs to see drafts and inactive records too.
        Route::middleware('role:'.implode(',', $artistMgmtRead))->group(function () {
            Route::get('artists', [ArtistAdminController::class, 'index']);
            Route::get('releases', [ReleaseAdminController::class, 'index']);
        });
        Route::middleware('role:'.implode(',', $artistMgmtWrite))->group(function () {
            Route::post('artists', [ArtistAdminController::class, 'store']);
            Route::patch('artists/{artist}', [ArtistAdminController::class, 'update']);
            Route::delete('artists/{artist}', [ArtistAdminController::class, 'destroy']);

            Route::post('releases', [ReleaseAdminController::class, 'store']);
            Route::patch('releases/{release}', [ReleaseAdminController::class, 'update']);
            Route::delete('releases/{release}', [ReleaseAdminController::class, 'destroy']);
        });

        // Rights Management (discovery.md §3) — Full: Super Admin, Manage: A&R,
        // Read: Management + Finance.
        Route::middleware('role:'.implode(',', $rightsRead))->group(function () {
            Route::get('rights-records', [RightsRecordAdminController::class, 'index']);
            Route::get('rights-records/{right}', [RightsRecordAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $rightsWrite))->group(function () {
            Route::post('rights-records', [RightsRecordAdminController::class, 'store']);
            Route::patch('rights-records/{right}', [RightsRecordAdminController::class, 'update']);
            Route::delete('rights-records/{right}', [RightsRecordAdminController::class, 'destroy']);
        });

        // Royalty Management (discovery.md §3) — Full: Super Admin, Manage:
        // Finance, Read: Management + A&R.
        Route::middleware('role:'.implode(',', $royaltyRead))->group(function () {
            Route::get('royalty-statements', [RoyaltyStatementAdminController::class, 'index']);
            Route::get('royalty-statements/{statement}', [RoyaltyStatementAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $royaltyWrite))->group(function () {
            Route::post('royalty-statements', [RoyaltyStatementAdminController::class, 'store']);
            Route::patch('royalty-statements/{statement}', [RoyaltyStatementAdminController::class, 'update']);
            Route::delete('royalty-statements/{statement}', [RoyaltyStatementAdminController::class, 'destroy']);
        });

        // Licensing Requests (discovery.md §3) — Full: Super Admin only,
        // Read: Management + A&R + Finance.
        Route::middleware('role:'.implode(',', $licensingRead))->group(function () {
            Route::get('licensing-requests', [LicensingRequestAdminController::class, 'index']);
            Route::get('licensing-requests/{licensingRequest}', [LicensingRequestAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $licensingWrite))->group(function () {
            Route::patch('licensing-requests/{licensingRequest}/status', [LicensingRequestAdminController::class, 'updateStatus']);
            Route::delete('licensing-requests/{licensingRequest}', [LicensingRequestAdminController::class, 'destroy']);
        });

        // Partnerships (discovery.md §3) — Full: Super Admin only, Read: Management only.
        Route::middleware('role:'.implode(',', $partnersRead))->group(function () {
            Route::get('partners', [PartnerAdminController::class, 'index']);
            Route::get('partners/{partner}', [PartnerAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $partnersWrite))->group(function () {
            Route::patch('partners/{partner}/status', [PartnerAdminController::class, 'updateStatus']);
            Route::delete('partners/{partner}', [PartnerAdminController::class, 'destroy']);
        });

        // Events & Bookings (discovery.md §3) — Full: Super Admin, Manage:
        // A&R, Read: Management + Content Manager. Admin index/show return
        // every status, including Cancelled, unlike the public GET /events.
        Route::middleware('role:'.implode(',', $eventsRead))->group(function () {
            Route::get('events', [EventAdminController::class, 'index']);
            Route::get('events/{event}', [EventAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $eventsWrite))->group(function () {
            Route::post('events', [EventAdminController::class, 'store']);
            Route::patch('events/{event}', [EventAdminController::class, 'update']);
            Route::delete('events/{event}', [EventAdminController::class, 'destroy']);
        });

        // Store (discovery.md §3) — Full: Super Admin, Manage: Content
        // Manager, Read: Management. Admin index/show return Draft items
        // too, unlike the public GET /products.
        Route::middleware('role:'.implode(',', $storeRead))->group(function () {
            Route::get('products', [ProductAdminController::class, 'index']);
            Route::get('products/{product}', [ProductAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $storeWrite))->group(function () {
            Route::post('products', [ProductAdminController::class, 'store']);
            Route::patch('products/{product}', [ProductAdminController::class, 'update']);
            Route::delete('products/{product}', [ProductAdminController::class, 'destroy']);
        });

        // Content Management (discovery.md §3) — Full: Super Admin, Manage:
        // Content Manager, Read: Management. Scoped to News (§2's only
        // modeled content entity); admin index/show return Draft posts too.
        Route::middleware('role:'.implode(',', $contentRead))->group(function () {
            Route::get('news', [NewsAdminController::class, 'index']);
            Route::get('news/{newsPost}', [NewsAdminController::class, 'show']);
        });
        Route::middleware('role:'.implode(',', $contentWrite))->group(function () {
            Route::post('news', [NewsAdminController::class, 'store']);
            Route::patch('news/{newsPost}', [NewsAdminController::class, 'update']);
            Route::delete('news/{newsPost}', [NewsAdminController::class, 'destroy']);
        });

        // Users & Roles (discovery.md §3) — Full: Super Admin only, every
        // other role gets "—". Roles are the fixed, code-defined set (read-
        // only, just to populate the picker on the Users form).
        Route::middleware('role:'.implode(',', $usersOnly))->group(function () {
            Route::get('roles', [RoleAdminController::class, 'index']);

            Route::get('users', [UserAdminController::class, 'index']);
            Route::get('users/{user}', [UserAdminController::class, 'show']);
            Route::post('users', [UserAdminController::class, 'store']);
            Route::patch('users/{user}', [UserAdminController::class, 'update']);
            Route::delete('users/{user}', [UserAdminController::class, 'destroy']);
        });

        // Audit Log (discovery.md §3) — Full: Super Admin, Read: Management.
        // Read-only; entries are written by AuditLogObserver, not this route.
        Route::middleware('role:'.implode(',', $auditLogRead))->group(function () {
            Route::get('audit-log', [AuditLogAdminController::class, 'index']);
        });

        // Analytics & Reports (discovery.md §3) — Full: Super Admin, Read:
        // Management, Read (own scope): A&R + Finance.
        Route::middleware('role:'.implode(',', $reportsRead))->group(function () {
            Route::get('reports/summary', [ReportsAdminController::class, 'summary']);
        });
    });
});
