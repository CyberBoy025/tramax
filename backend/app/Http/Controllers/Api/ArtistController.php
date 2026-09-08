<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use Illuminate\Http\JsonResponse;

class ArtistController extends Controller
{
    public function index(): JsonResponse
    {
        $artists = ArtistProfile::query()
            ->where('status', '!=', 'Inactive')
            ->orderBy('artist_name')
            ->get(['id', 'artist_name', 'slug', 'genre', 'photo_url', 'status']);

        return response()->json(['data' => $artists]);
    }

    public function show(string $slug): JsonResponse
    {
        $artist = ArtistProfile::query()
            ->where('slug', $slug)
            ->with(['releases' => fn ($q) => $q->where('status', 'Published')->orderByDesc('release_date')])
            ->firstOrFail();

        return response()->json(['data' => $artist]);
    }
}
