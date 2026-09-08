<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Gated to Super Administrator + A&R / Artist Manager per discovery.md §3.
// Destructive delete is Super Administrator only — "Full" vs "Manage" in that matrix.
class ArtistAdminController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'artist_name' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Active,Development,Inactive'],
        ]);

        $data['slug'] = Str::slug($data['artist_name']).'-'.Str::random(4);
        $artist = ArtistProfile::create($data);

        return response()->json(['data' => $artist], 201);
    }

    public function update(Request $request, ArtistProfile $artist): JsonResponse
    {
        $data = $request->validate([
            'artist_name' => ['sometimes', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Active,Development,Inactive'],
        ]);

        $artist->update($data);

        return response()->json(['data' => $artist->fresh()]);
    }

    public function destroy(Request $request, ArtistProfile $artist): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete an artist.'], 403);
        }

        $artist->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }
}
