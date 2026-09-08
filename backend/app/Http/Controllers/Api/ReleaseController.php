<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\JsonResponse;

class ReleaseController extends Controller
{
    public function index(): JsonResponse
    {
        $releases = Release::query()
            ->where('status', 'Published')
            ->with('artist:id,artist_name,slug')
            ->orderByDesc('release_date')
            ->get(['id', 'artist_profile_id', 'title', 'slug', 'type', 'cover_art_url', 'release_date']);

        return response()->json(['data' => $releases]);
    }

    public function show(string $slug): JsonResponse
    {
        $release = Release::query()
            ->where('slug', $slug)
            ->with(['artist:id,artist_name,slug', 'tracks'])
            ->firstOrFail();

        return response()->json(['data' => $release]);
    }
}
