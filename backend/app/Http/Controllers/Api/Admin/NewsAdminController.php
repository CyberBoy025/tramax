<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Read gated to Super Administrator, Management, Content Manager; write
// gated to Super Administrator + Content Manager; delete further
// restricted to Super Administrator — per discovery.md §3's Content
// Management row. Scoped to News (the only entity §2 actually models);
// generic Pages/Media management from proposal §4.3 isn't built.
// Unlike the public GET /news, this returns Draft posts too.
class NewsAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => NewsPost::query()->orderByDesc('created_at')->get()]);
    }

    public function show(NewsPost $newsPost): JsonResponse
    {
        return response()->json(['data' => $newsPost]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $data['author_id'] = $request->user()->id;
        if (($data['status'] ?? null) === 'Published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = NewsPost::create($data);

        return response()->json(['data' => $post], 201);
    }

    public function update(Request $request, NewsPost $newsPost): JsonResponse
    {
        $data = $this->validated($request, sometimes: true);
        if (($data['status'] ?? null) === 'Published' && ! $newsPost->published_at) {
            $data['published_at'] = now();
        }

        $newsPost->update($data);

        return response()->json(['data' => $newsPost->fresh()]);
    }

    public function destroy(Request $request, NewsPost $newsPost): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete a news post.'], 403);
        }

        $newsPost->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['required', $r];

        return $request->validate([
            'title' => $rule('string'),
            'body' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Draft,Published'],
        ]);
    }
}
