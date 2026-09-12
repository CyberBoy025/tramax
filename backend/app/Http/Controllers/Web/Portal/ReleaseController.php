<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Portal\PortalReleaseController — an artist can
// submit a release, always landing as Draft; only an admin publishes it.
class ReleaseController extends Controller
{
    private const TYPES = ['Single', 'EP', 'Album'];

    public function index(Request $request): View
    {
        $profile = $request->user()->artistProfile;

        $releases = $profile
            ? $profile->releases()->orderByDesc('release_date')->get()
            : null;

        return view('portal.releases.index', ['profile' => $profile, 'releases' => $releases, 'types' => self::TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->artistProfile;
        if (! $profile) {
            return back()->with('error', "Your account isn't linked to an artist profile yet.");
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', self::TYPES)],
            'cover_art_url' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
        ]);

        $data['artist_profile_id'] = $profile->id;
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $data['status'] = 'Draft';

        Release::create($data);

        return redirect()->route('portal.releases.index')->with('success', 'Release submitted for review.');
    }
}
