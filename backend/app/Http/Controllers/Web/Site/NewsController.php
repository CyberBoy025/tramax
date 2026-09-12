<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/news/page.tsx and
// news/[slug]/page.tsx — same public-scoped queries as Api\NewsController.
class NewsController extends Controller
{
    public function index(): View
    {
        return view('site.news.index', [
            'posts' => NewsPost::query()->where('status', 'Published')->orderByDesc('published_at')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = NewsPost::query()->where('slug', $slug)->firstOrFail();

        return view('site.news.show', ['post' => $post]);
    }
}
