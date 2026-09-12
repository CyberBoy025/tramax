<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\Release;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\ReleaseAdminController.
class ReleaseController extends Controller
{
    private const TYPES = ['Single', 'EP', 'Album'];

    private const STATUSES = ['Draft', 'Processing', 'Published'];

    public function index(): View
    {
        return view('admin.releases.index', [
            'releases' => Release::query()->with('artist:id,artist_name')->orderByDesc('release_date')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.releases.form', [
            'release' => null,
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(['id', 'artist_name']),
            'types' => self::TYPES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'artist_profile_id' => ['required', 'exists:artist_profiles,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', self::TYPES)],
            'cover_art_url' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        Release::create($data);

        return redirect()->route('admin.releases.index')->with('success', 'Release created.');
    }

    public function edit(Release $release): View
    {
        return view('admin.releases.form', [
            'release' => $release,
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(['id', 'artist_name']),
            'types' => self::TYPES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Release $release): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:'.implode(',', self::TYPES)],
            'cover_art_url' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $release->update($data);

        return redirect()->route('admin.releases.index')->with('success', 'Release updated.');
    }

    public function destroy(Request $request, Release $release): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete a release.');
        }

        $release->delete();

        return redirect()->route('admin.releases.index')->with('success', 'Release deleted.');
    }
}
