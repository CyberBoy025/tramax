<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistApplication;
use App\Models\ArtistProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Gated to Super Administrator + A&R / Artist Manager per discovery.md §3
// ("Artist Management" row) via the 'role' middleware on routes/api.php.
class ApplicationAdminController extends Controller
{
    private const STATUSES = ['Submitted', 'Under Review', 'Shortlisted', 'Accepted', 'Rejected'];

    public function index(Request $request): JsonResponse
    {
        $query = ArtistApplication::query()->orderByDesc('submitted_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(ArtistApplication $application): JsonResponse
    {
        return response()->json(['data' => $application]);
    }

    public function updateStatus(Request $request, ArtistApplication $application): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $application->update($data);

        // Accepting an application provisions the artist's public profile —
        // the hand-off point between "Artist Management" and the live catalogue.
        if ($data['status'] === 'Accepted') {
            ArtistProfile::firstOrCreate(
                ['slug' => Str::slug($application->artist_name)],
                [
                    'artist_name' => $application->artist_name,
                    'genre' => $application->genre,
                    'biography' => $application->biography,
                    'status' => 'Development',
                ]
            );
        }

        return response()->json(['data' => $application->fresh()]);
    }
}
