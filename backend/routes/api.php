<?php

use App\Http\Controllers\Api\Admin\ApplicationAdminController;
use App\Http\Controllers\Api\Admin\ArtistAdminController;
use App\Http\Controllers\Api\Admin\ReleaseAdminController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\LicensingRequestController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ReleaseController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public-site endpoints per discovery.md §4.1 — no auth required.
    Route::get('artists', [ArtistController::class, 'index']);
    Route::get('artists/{slug}', [ArtistController::class, 'show']);

    Route::get('releases', [ReleaseController::class, 'index']);
    Route::get('releases/{slug}', [ReleaseController::class, 'show']);

    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{slug}', [EventController::class, 'show']);

    Route::get('news', [NewsController::class, 'index']);
    Route::get('news/{slug}', [NewsController::class, 'show']);

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

    // Admin platform — gated per discovery.md §3's RBAC matrix. Only the
    // Artist Management and Music Catalogue rows are built out so far;
    // Rights, Royalty, Licensing, Events, Store, Content, Partners, Users,
    // and Audit Log admin endpoints are a follow-up.
    Route::middleware(['auth:sanctum', 'role:'.Role::SUPER_ADMIN.','.Role::AR_MANAGER])
        ->prefix('admin')
        ->group(function () {
            Route::get('applications', [ApplicationAdminController::class, 'index']);
            Route::get('applications/{application}', [ApplicationAdminController::class, 'show']);
            Route::patch('applications/{application}/status', [ApplicationAdminController::class, 'updateStatus']);

            Route::post('artists', [ArtistAdminController::class, 'store']);
            Route::patch('artists/{artist}', [ArtistAdminController::class, 'update']);
            Route::delete('artists/{artist}', [ArtistAdminController::class, 'destroy']);

            Route::post('releases', [ReleaseAdminController::class, 'store']);
            Route::patch('releases/{release}', [ReleaseAdminController::class, 'update']);
            Route::delete('releases/{release}', [ReleaseAdminController::class, 'destroy']);
        });
});
