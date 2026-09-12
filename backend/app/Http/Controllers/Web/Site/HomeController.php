<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\ArtistProfile;
use App\Models\Release;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/page.tsx — same public-scoped
// queries as Api\ArtistController::index / Api\ReleaseController::index.
class HomeController extends Controller
{
    public function index(): View
    {
        $artists = ArtistProfile::query()->where('status', '!=', 'Inactive')->orderBy('artist_name')->get();
        $releases = Release::query()->where('status', 'Published')->get();

        return view('site.home', [
            'stats' => [
                ['label' => 'Artists', 'value' => (string) $artists->count()],
                ['label' => 'Releases', 'value' => (string) $releases->count()],
                ['label' => 'Cities Played', 'value' => '—'],
                ['label' => 'Years Active', 'value' => '—'],
            ],
            'featuredArtists' => $artists->take(3),
        ]);
    }
}
