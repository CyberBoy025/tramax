<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Portal\PortalProfileController — same scoping
// (the authenticated user's own ArtistProfile) and the same admin/self
// field split: artist_name/slug/status stay admin-controlled.
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('portal.profile', ['profile' => $request->user()->artistProfile]);
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return back()->with('error', "Your account isn't linked to an artist profile yet.");
        }

        $data = $request->validate([
            'biography' => ['nullable', 'string'],
            'genre' => ['nullable', 'string', 'max:255'],
            'photo_url' => ['nullable', 'string', 'max:255'],
        ]);

        $profile->update($data);

        return back()->with('success', 'Profile updated.');
    }
}
