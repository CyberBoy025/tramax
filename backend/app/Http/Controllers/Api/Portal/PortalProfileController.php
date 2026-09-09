<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Artist Management & Applications (discovery.md §3) — Own (profile) for
// the Artist role. Scoped to the authenticated user's linked ArtistProfile;
// artist_name/slug/status stay admin-controlled (Users & Roles-style split
// between what the account owner can touch and what only an admin can).
class PortalProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->artistProfile]);
    }

    public function update(Request $request): JsonResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return response()->json(['message' => 'Your account isn\'t linked to an artist profile yet.'], 422);
        }

        $data = $request->validate([
            'biography' => ['nullable', 'string'],
            'genre' => ['nullable', 'string', 'max:255'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['string', 'max:255'],
        ]);

        $profile->update($data);

        return response()->json(['data' => $profile->fresh()]);
    }
}
