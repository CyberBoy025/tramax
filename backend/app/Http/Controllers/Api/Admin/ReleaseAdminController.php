<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Gated to Super Administrator + A&R / Artist Manager per discovery.md §3
// ("Music Catalogue" row). Destructive delete is Super Administrator only.
class ReleaseAdminController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'artist_profile_id' => ['required', 'exists:artist_profiles,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Single,EP,Album'],
            'cover_art_url' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:Draft,Processing,Published'],
        ]);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $release = Release::create($data);

        return response()->json(['data' => $release], 201);
    }

    public function update(Request $request, Release $release): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:Single,EP,Album'],
            'cover_art_url' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:Draft,Processing,Published'],
        ]);

        $release->update($data);

        return response()->json(['data' => $release->fresh()]);
    }

    public function destroy(Request $request, Release $release): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete a release.'], 403);
        }

        $release->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }
}
