<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\ArtistApplication;
use App\Models\ArtistProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/artists/page.tsx and
// artists/[slug]/page.tsx — same public-scoped queries as
// Api\ArtistController, plus the "submit your music" form that lives on
// the artists index page (Api\ApplicationController::store's validation,
// ported verbatim).
class ArtistController extends Controller
{
    public function index(): View
    {
        return view('site.artists.index', [
            'artists' => ArtistProfile::query()->where('status', '!=', 'Inactive')->orderBy('artist_name')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $artist = ArtistProfile::query()
            ->where('slug', $slug)
            ->with(['releases' => fn ($q) => $q->where('status', 'Published')->orderByDesc('release_date')])
            ->firstOrFail();

        return view('site.artists.show', ['artist' => $artist]);
    }

    public function storeApplication(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'artist_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'years_active' => ['nullable', 'integer', 'min:0', 'max:100'],
            'biography' => ['nullable', 'string'],
        ]);

        ArtistApplication::create($data + ['submitted_at' => now()]);

        return redirect(route('site.artists.index').'#submit')->with('submitted', true);
    }
}
