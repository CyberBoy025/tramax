<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/music/page.tsx and
// music/[slug]/page.tsx — same public-scoped queries as Api\ReleaseController.
class ReleaseController extends Controller
{
    public function index(): View
    {
        return view('site.music.index', [
            'releases' => Release::query()->where('status', 'Published')->with('artist:id,artist_name,slug')->orderByDesc('release_date')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $release = Release::query()
            ->where('slug', $slug)
            ->with(['artist:id,artist_name,slug', 'tracks' => fn ($q) => $q->orderBy('track_number')])
            ->firstOrFail();

        return view('site.music.show', ['release' => $release]);
    }
}
