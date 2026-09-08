<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = NewsPost::query()
            ->where('status', 'Published')
            ->orderByDesc('published_at')
            ->get(['id', 'title', 'slug', 'cover_image', 'published_at']);

        return response()->json(['data' => $posts]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = NewsPost::query()->where('slug', $slug)->firstOrFail();

        return response()->json(['data' => $post]);
    }
}
