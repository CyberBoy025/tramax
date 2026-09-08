<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\LicensingRequestController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ReleaseController;
use Illuminate\Support\Facades\Route;

// Public-site endpoints per discovery.md §4.1. Artist portal (§4.2) and
// admin platform (§4.3) endpoints are a separate pass — they need auth/RBAC
// (discovery.md §3) which isn't built yet.
Route::prefix('v1')->group(function () {
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
});
