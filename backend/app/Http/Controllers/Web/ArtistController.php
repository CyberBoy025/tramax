<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\ArtistAdminController — same queries,
// same validation, same statuses. Create/edit are separate pages here
// (routes admin/artists/create, admin/artists/{artist}/edit) rather than
// one page that toggles a form, which is the natural Blade/multi-page
// shape for this instead of forcing the old SPA-style single-page pattern.
class ArtistController extends Controller
{
    private const STATUSES = ['Active', 'Development', 'Inactive'];

    public function index(): View
    {
        return view('admin.artists.index', [
            'artists' => ArtistProfile::query()->orderBy('artist_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.artists.form', ['artist' => null, 'statuses' => self::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'artist_name' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $data['slug'] = Str::slug($data['artist_name']).'-'.Str::random(4);
        ArtistProfile::create($data);

        return redirect()->route('admin.artists.index')->with('success', 'Artist created.');
    }

    public function edit(ArtistProfile $artist): View
    {
        return view('admin.artists.form', ['artist' => $artist, 'statuses' => self::STATUSES]);
    }

    public function update(Request $request, ArtistProfile $artist): RedirectResponse
    {
        $data = $request->validate([
            'artist_name' => ['sometimes', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $artist->update($data);

        return redirect()->route('admin.artists.index')->with('success', 'Artist updated.');
    }

    public function destroy(Request $request, ArtistProfile $artist): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete an artist.');
        }

        $artist->delete();

        return redirect()->route('admin.artists.index')->with('success', 'Artist deleted.');
    }
}
