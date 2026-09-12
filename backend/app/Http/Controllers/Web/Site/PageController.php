<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

// Static-content pages with no backing data — ports about/page.tsx and
// videos/page.tsx.
class PageController extends Controller
{
    public function about(): View
    {
        return view('site.about');
    }

    public function videos(): View
    {
        return view('site.videos', [
            'categories' => ['Official Videos', 'Lyric Videos', 'Behind The Scenes', 'Interviews', 'Live Performances'],
        ]);
    }
}
