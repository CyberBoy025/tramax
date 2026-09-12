<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\NewsAdminController — same
// publish-sets-published_at-once rule.
class NewsController extends Controller
{
    private const STATUSES = ['Draft', 'Published'];

    public function index(): View
    {
        return view('admin.content.index', ['posts' => NewsPost::query()->orderByDesc('created_at')->get()]);
    }

    public function create(): View
    {
        return view('admin.content.form', ['post' => null, 'statuses' => self::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $data['author_id'] = $request->user()->id;
        if (($data['status'] ?? null) === 'Published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        NewsPost::create($data);

        return redirect()->route('admin.content.index')->with('success', 'News post created.');
    }

    public function edit(NewsPost $post): View
    {
        return view('admin.content.form', ['post' => $post, 'statuses' => self::STATUSES]);
    }

    public function update(Request $request, NewsPost $post): RedirectResponse
    {
        $data = $this->validated($request, sometimes: true);
        if (($data['status'] ?? null) === 'Published' && ! $post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('admin.content.index')->with('success', 'News post updated.');
    }

    public function destroy(Request $request, NewsPost $post): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete a news post.');
        }

        $post->delete();

        return redirect()->route('admin.content.index')->with('success', 'News post deleted.');
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['required', $r];

        return $request->validate([
            'title' => $rule('string'),
            'body' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);
    }
}
