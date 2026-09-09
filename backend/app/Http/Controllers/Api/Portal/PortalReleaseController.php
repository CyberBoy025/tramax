<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Music Catalogue / Releases (discovery.md §3) — Own (submit/track) for the
// Artist role: an artist can see and submit their own releases, not
// publish them. Rights records ride along nested on each release rather
// than getting a separate portal endpoint — they're release-scoped data,
// and Rights Management's own matrix row is Own (read) for Artist too.
class PortalReleaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return response()->json(['data' => []]);
        }

        $releases = $profile->releases()
            ->with(['tracks', 'rightsRecords'])
            ->orderByDesc('release_date')
            ->get();

        return response()->json(['data' => $releases]);
    }

    public function store(Request $request): JsonResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return response()->json(['message' => 'Your account isn\'t linked to an artist profile yet.'], 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Single,EP,Album'],
            'cover_art_url' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
        ]);

        $data['artist_profile_id'] = $profile->id;
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        // Always submitted as Draft — an artist can submit for review, only
        // an admin publishes (Music Catalogue's Full/Manage tiers), per the
        // "submit" half of "Own (submit/track)".
        $data['status'] = 'Draft';

        $release = Release::create($data);

        return response()->json(['data' => $release], 201);
    }
}
